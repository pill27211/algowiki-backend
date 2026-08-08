#include <bits/stdc++.h>
#include "/usr/include/mysql/mysql.h"
using ll = long long;
using namespace std;

random_device rd;
mt19937 gen(rd());


int get_user_rating(MYSQL * conn, char * user_id)
{
	char query[1001]{};
	sprintf(query, "select ifNULL(sum(t.difficulty), 0) from ( select p.difficulty from accept a join problem p ON a.problem_id = p.problem_id where a.user_id = '%s' order by p.difficulty DESC limit 50 ) t", user_id);
	mysql_real_query(conn, query, strlen(query));

	MYSQL_RES * result = mysql_store_result(conn);
	if(result == NULL) return 0;

	MYSQL_ROW row = mysql_fetch_row(result);

	int rating(atoi(row[0]));
	mysql_free_result(result);

	return rating / 40;
}
int check(MYSQL * conn, char * user_id)
{
	char query[1001]{};
	sprintf(query, "select count(*) from progress where user_id = '%s' and quest_class = 1 and user_prog < quest_end_prog", user_id);
	mysql_real_query(conn, query, strlen(query));

	MYSQL_RES * result = mysql_store_result(conn);
	if(result == NULL) return 1;
	
	MYSQL_ROW row = mysql_fetch_row(result);
	int flag = atoi(row[0]) == 0;
	
	mysql_free_result(result);
	return flag;
}
int tagCheck(MYSQL * conn, char * user_id, string tag)
{
	char query[1001]{};
	sprintf(query, "SELECT COUNT(*) FROM %s x LEFT JOIN accept y ON x.problem_id = y.problem_id and y.user_id = '%s' WHERE y.problem_id IS NULL and x.defunct = 'N'", tag.c_str(), user_id);
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

string getRandTag(MYSQL * conn, char * user_id, int rating)
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

	if(!vec.size()) return "";
	vSwap(vec);
	std::uniform_int_distribution<ll> ran(0, vec.size() - 1);
	return vec[ran(gen)];
}

void random_tag_gen(MYSQL * conn, char * user_id, int rating)
{
	char query[1001]{};
	sprintf(query, "select * from progress where user_id = '%s' and quest_id = 23", user_id);
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
		sprintf(query, "update progress set sub_content = '-', quest_rec_rewards = 1, quest_sort_weight = -1 where user_id = '%s' and quest_id = 23;", user_id);
		mysql_real_query(conn, query, strlen(query));
		return;
	}

	sprintf(query, "update progress SET sub_content = '%s' WHERE quest_id = 23 and user_id = '%s'", tag.c_str(), user_id);
	mysql_real_query(conn, query, strlen(query));

	mysql_free_result(result);
}
void random_problem_gen(MYSQL * conn, char *user_id, int quest_id, int diff)
{
	char query[1001]{};
	sprintf(query, "select * from progress where user_id = '%s' and quest_id = %d", user_id, quest_id);
	mysql_real_query(conn, query, strlen(query));

	MYSQL_RES * result = mysql_store_result(conn);
	if(result == NULL) return;

	MYSQL_ROW row = mysql_fetch_row(result);
	if(atoi(row[2]) == atoi(row[3]))
	{
		mysql_free_result(result);
		return;
	}
	mysql_free_result(result);
	
	sprintf(query, "select problem_id from problem where difficulty in(%d, %d) and defunct = 'N' and problem_id not in ( select problem_id from accept where user_id = '%s' )", diff, diff + 1, user_id);
	mysql_real_query(conn, query, strlen(query));

	result = mysql_store_result(conn);
	if(result == NULL) return;

	vector <int> problem_list;
	row = NULL;
	while((row = mysql_fetch_row(result)) != NULL)
		problem_list.push_back(row[0] ? atoi(row[0]) : 0);

	if(problem_list.size() == 0)
	{
		sprintf(query, "update progress set sub_content = '-', quest_rec_rewards = 1, quest_sort_weight = -1 where quest_id = %d and user_id = '%s'", quest_id, user_id);
		mysql_real_query(conn, query, strlen(query));
		return;
	}

	std::random_device rd;
	std::mt19937 gen(rd());
	std::uniform_int_distribution<int> generator(0, (int)problem_list.size() - 1);
	vSwap(problem_list);

	int problem_id = generator(gen);
	sprintf(query, "update progress set sub_content = '%d' where quest_id = %d and user_id = '%s'", problem_list[problem_id], quest_id, user_id);
	mysql_real_query(conn, query, strlen(query));

	mysql_free_result(result);
}
int main(int argc, char *argv[])
{
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

	int rating(get_user_rating(conn, argv[1]));

	if(check(conn, argv[1]) > 0) return 1;

	random_tag_gen(conn, argv[1], rating);
	int quest_id(64);
	for(int diff : { 3, 5, 7 })
		random_problem_gen(conn, argv[1], quest_id++, diff);

	return 0;
}