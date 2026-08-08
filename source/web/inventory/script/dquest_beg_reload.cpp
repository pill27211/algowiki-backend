#include <bits/stdc++.h>
#include "/usr/include/mysql/mysql.h"
using ll = long long;
using namespace std;

random_device rd;
mt19937 gen(rd());

int check(MYSQL * conn, char * user_id)
{
	char query[1001]{};
	sprintf(query, "select count(*) from progress where user_id = '%s' and quest_id = 64 and user_prog < quest_end_prog", user_id);
	mysql_real_query(conn, query, strlen(query));

	MYSQL_RES * result = mysql_store_result(conn);
	if(result == NULL) return 1;
	
	MYSQL_ROW row = mysql_fetch_row(result);
	int flag = atoi(row[0]) == 0;
	
	mysql_free_result(result);
	return flag;
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
int random_problem_gen(MYSQL * conn, char *user_id, int quest_id, int diff)
{
	char query[1001]{};	
	sprintf(query, "select problem_id from problem where difficulty in(%d, %d) and defunct = 'N' and problem_id not in ( select problem_id from accept where user_id = '%s' )", diff, diff + 1, user_id);
	mysql_real_query(conn, query, strlen(query));

	MYSQL_RES * result = mysql_store_result(conn);
	if(result == NULL) return 1;

	vector <int> problem_list;
	MYSQL_ROW row = NULL;
	while((row = mysql_fetch_row(result)) != NULL)
		problem_list.push_back(row[0] ? atoi(row[0]) : 0);

	if(problem_list.size() == 0)
	{
		if(result != NULL) mysql_free_result(result);
		return 1;
	}

	std::uniform_int_distribution<int> generator(0, (int)problem_list.size() - 1);
	vSwap(problem_list);

	int problem_id = generator(gen);
	sprintf(query, "update progress set sub_content = '%d' where quest_id = %d and user_id = '%s'", problem_list[problem_id], quest_id, user_id);
	mysql_real_query(conn, query, strlen(query));

	mysql_free_result(result);
	return 0;
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

	if(check(conn, argv[1]) > 0) return 1;
	if(random_problem_gen(conn, argv[1], 64, 3)) return 1;
	return 0;
}