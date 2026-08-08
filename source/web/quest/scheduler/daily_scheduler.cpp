#include <bits/stdc++.h>
#include "/usr/include/mysql/mysql.h"
using ll = long long;
using namespace std;

random_device rd;
mt19937 gen(rd());

void pinit()
{
	ifstream in( "/home/judge/data/1220/sample.out");
	int num; in >> num;
	in.close();

	ofstream out("/home/judge/data/1220/sample.out");
	out << ++num;
	out.close();
}
void daily_quest_init(MYSQL * conn)
{
	char query[1001]{};
	sprintf(query, "update progress set user_prog = 0, quest_rec_rewards = 0, quest_sort_weight = 0 where quest_class = 1");
	mysql_real_query(conn, query, strlen(query));
}

void get_user_name(MYSQL * conn, vector <string>& vec)
{
	char query[1001]{};
	sprintf(query, "select user_id from users");
	mysql_real_query(conn, query, strlen(query));

	MYSQL_RES * result = mysql_store_result(conn);
	if(result == NULL) return;

	MYSQL_ROW row = NULL;
	while((row = mysql_fetch_row(result)) != NULL)
		vec.push_back(row[0] ? row[0] : "");

	mysql_free_result(result);
}
void get_user_rating(MYSQL * conn, vector <int>& vec, string user_id)
{
	char query[1001]{};
	sprintf(query, "select ifNULL(sum(t.difficulty), 0) from ( select p.difficulty from accept a join problem p ON a.problem_id = p.problem_id where a.user_id = '%s' order by p.difficulty DESC limit 50 ) t", user_id.c_str());
	mysql_real_query(conn, query, strlen(query));

	MYSQL_RES * result = mysql_store_result(conn);
	if(result == NULL) return;

	MYSQL_ROW row = mysql_fetch_row(result);

	int rating(atoi(row[0]));
	mysql_free_result(result);

	vec.push_back(rating / 40);
}
int tagCheck(MYSQL * conn, string user_id, string tag)
{
	char query[1001]{};
	sprintf(query, "SELECT COUNT(*) FROM %s x LEFT JOIN accept y ON x.problem_id = y.problem_id and y.user_id = '%s' WHERE y.problem_id IS NULL and x.defunct = 'N'", tag.c_str(), user_id.c_str());
	mysql_real_query(conn, query, strlen(query));

	MYSQL_RES * result = mysql_store_result(conn);
	if(result == NULL) return 1;

	MYSQL_ROW row = mysql_fetch_row(result);
	int flag(atoi(row[0]) == 0);
	mysql_free_result(result);

	return flag;
}
void vSwap(vector <string>& vec)
{
	if(!vec.size()) return;

	std::uniform_int_distribution<ll> swc(100, 200);
	std::uniform_int_distribution<ll> left(0, (int)vec.size() / 2);
	std::uniform_int_distribution<ll> right(vec.size() / 2, vec.size() - 1);

	for(int i(vec.size() * swc(gen)); i > 0; i--)
		swap(vec[left(gen)], vec[right(gen)]);
}
void vSwap(vector <int>& vec)
{
	if(!vec.size()) return;

	std::uniform_int_distribution<ll> swc(100, 200);
	std::uniform_int_distribution<ll> left(0, (int)vec.size() / 2);
	std::uniform_int_distribution<ll> right(vec.size() / 2, vec.size() - 1);

	for(int i(vec.size() * swc(gen)); i > 0; i--)
		swap(vec[left(gen)], vec[right(gen)]);
}
int getTagMindiff(MYSQL * conn, string tag)
{
	char query[1001]{};
	sprintf(query, "select min(difficulty) from %s", tag.c_str());
	mysql_real_query(conn, query, strlen(query));

	MYSQL_RES * result = mysql_store_result(conn);
	if(result == NULL) return 12;

	MYSQL_ROW row = mysql_fetch_row(result);

	int diff(atoi(row[0]));
	mysql_free_result(result);

	return diff;
}
string getRandTag(MYSQL * conn, string user_id, int rating)
{
	vector <string> vec;

	char query[1001]{};
	sprintf(query, "select tag_name from tag");
	mysql_real_query(conn, query, strlen(query));

	MYSQL_RES * result = mysql_store_result(conn);
	MYSQL_ROW row = NULL;
	while((row = mysql_fetch_row(result)) != NULL)
	{
		string tag(row[0] ? row[0] : "");
		if(tagCheck(conn, user_id, tag)) continue;
		if(getTagMindiff(conn, tag) - 2 > rating) continue;

		vec.emplace_back(tag);
	}
	mysql_free_result(result);

	/*printf("%s %d\n", user_id.c_str(), rating);
	for(auto& str : vec)
		printf("%s\n", str.c_str());
	printf("\n");*/

	if(!vec.size()) return "";
	vSwap(vec);
	std::uniform_int_distribution<ll> ran(0, vec.size() - 1);
	return vec[ran(gen)];
}
void random_tag_gen(MYSQL * conn, string user_id, int rating)
{
	char query[1001]{};
	sprintf(query, "select * from progress where user_id = '%s' and quest_id = 23", user_id.c_str());
	mysql_real_query(conn, query, strlen(query));

	MYSQL_RES * result = mysql_store_result(conn);
	if(result == NULL) return;

	MYSQL_ROW row = mysql_fetch_row(result);
	if(atoi(row[2]) == atoi(row[3]))
	{
		mysql_free_result(result);
		return;
	}

	string tag(getRandTag(conn, user_id, rating));
	if(tag == "")
	{
		mysql_free_result(result);
		sprintf(query, "update progress set sub_content = '-', quest_rec_rewards = 1, quest_sort_weight = -1 where user_id = '%s' and quest_id = 23;", user_id.c_str());
		mysql_real_query(conn, query, strlen(query));
		return;
	}

	sprintf(query, "update progress SET sub_content = '%s' WHERE quest_id = 23 and user_id = '%s'", tag.c_str(), user_id.c_str());
	mysql_real_query(conn, query, strlen(query));

	mysql_free_result(result);
}

void random_problem_gen(MYSQL * conn, string name, int quest_id, int diff)
{
	char query[1001]{};
	sprintf(query, "select problem_id from problem where difficulty in(%d, %d) and defunct = 'N' and problem_id not in ( select problem_id from accept where user_id = '%s' )", diff, diff + 1, name.c_str());
	mysql_real_query(conn, query, strlen(query));

	MYSQL_RES * result = mysql_store_result(conn);
	if(result == NULL) return;

	vector <int> problem_list;
	MYSQL_ROW row = NULL;
	while((row = mysql_fetch_row(result)) != NULL)
		problem_list.push_back(row[0] ? atoi(row[0]) : 0);

	if(problem_list.size() == 0)
	{
		sprintf(query, "update progress set sub_content = '-', quest_rec_rewards = 1, quest_sort_weight = -1 where quest_id = %d and user_id = '%s'", quest_id, name.c_str());
		mysql_real_query(conn, query, strlen(query));
		return;
	}

	std::random_device rd;
	std::mt19937 gen(rd());
	std::uniform_int_distribution<int> generator(0, (int)problem_list.size() - 1);
	vSwap(problem_list);

	int problem_id = generator(gen);
	sprintf(query, "update progress set sub_content = '%d' where quest_id = %d and user_id = '%s'", problem_list[problem_id], quest_id, name.c_str());
	mysql_real_query(conn, query, strlen(query));

	mysql_free_result(result);
}
int main()
{
	pinit();

	//db info
	const char * server = "localhost";
   	const char * user = "hustoj";
   	const char * password = "__REDACTED__";
	const char * database = "jol";

	//db connect
	MYSQL * conn = mysql_init(NULL);
	if (mysql_real_connect(conn, server, user, password, database, 0, NULL, 0) == NULL)
	{
        	fprintf(stderr, "mysql_real_connect() ½ÇÆÐ: %s\n", mysql_error(conn));
        	mysql_close(conn);
        	return 1;
    	}

	//daily_quest initialization
	daily_quest_init(conn);

	//get user_names
	vector <string> user_names;
	get_user_name(conn, user_names);

	//get user_rating
	vector <int> user_rat;
	for(string& name : user_names)
		get_user_rating(conn, user_rat, name);

	//random_gen
	for(int i{}; i < (int)user_names.size(); i++)
	{
		
		random_tag_gen(conn, user_names[i], user_rat[i]);
		int quest_id(64);
		for(int diff : { 3, 5, 7 })
			random_problem_gen(conn, user_names[i], quest_id++, diff);
	}

	mysql_close(conn);
}