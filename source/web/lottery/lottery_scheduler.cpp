#include <bits/stdc++.h>
#include "/usr/include/mysql/mysql.h"
using ll = long long;
using namespace std;

std::random_device rd;
std::mt19937 gen(rd());

string f(vector <string>& vec)
{
	std::uniform_int_distribution<int> swc(10, 20);
	std::uniform_int_distribution<int> lll(0, (int)vec.size() / 2);
	std::uniform_int_distribution<int> rrr((int)vec.size() / 2, max((int)vec.size() / 2, (int)vec.size() - 1));
	for(int i(vec.size() * swc(gen)); i > 0; i--)
		swap(vec[lll(gen)], vec[rrr(gen)]);

	std::uniform_int_distribution<int> ran(0, vec.size() - 1);
	return vec[ran(gen)];
}
void ProgressUpdate(MYSQL * conn, vector <string>& vec)
{
	int new_user_prog = min(stoi(vec[2]) + 1, stoi(vec[3]));
	int new_quest_sort_weight = new_user_prog == stoi(vec[3]);
	char query[1001] = {};

	if(stoi(vec[2]) < stoi(vec[3]))
	{
		sprintf(query, "insert into content_log values('%s', 8, '%s 의 퀘스트 %s 진척도 변화', '%s 의 %s 번 진척도 %d', now())", vec[0].c_str(), vec[0].c_str(), vec[1].c_str(), vec[0].c_str(), vec[1].c_str(), new_user_prog);
		mysql_real_query(conn, query, strlen(query));
	}
	sprintf(query, "update progress set user_prog = %d, quest_sort_weight = %d where progress_id = %s", new_user_prog, new_quest_sort_weight, vec[5].c_str());
	mysql_real_query(conn, query, strlen(query));
}
void getProgress(MYSQL * conn, vector <string>& vec, string user_id)
{
	char query[1001]{};
	sprintf(query, "select * from progress where user_id = '%s' and quest_id = 90", user_id.c_str());
	mysql_real_query(conn, query, strlen(query));

	MYSQL_RES * result = mysql_store_result(conn);
	if(result == NULL) return;

	MYSQL_ROW row = mysql_fetch_row(result);
	for(int i{}; i < 9; i++)
		vec.push_back(row[i] ? string(row[i]) : "");

	mysql_free_result(result);
}
int getCoinInfo(MYSQL * conn, string user_id)
{
	char query[1001]{};
	sprintf(query, "select coin from uinfo where user_id = '%s'", user_id.c_str());
	mysql_real_query(conn, query, strlen(query));

	MYSQL_RES * result = mysql_store_result(conn);
	if(result == NULL) return 0;

	MYSQL_ROW row = mysql_fetch_row(result);
	int coin(atoi(row[0]));

	mysql_free_result(result);
	return coin;
}
int main()
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
        	fprintf(stderr, "mysql_real_connect() 실패: %s\n", mysql_error(conn));
        	mysql_close(conn);
        	return 1;
    	}

	char query[1001]{};
	sprintf(query, "select * from lottery");
	mysql_real_query(conn, query, strlen(query));

	MYSQL_RES * result = mysql_store_result(conn);
	if(result == NULL) return 0;

	vector <string> vec;
	MYSQL_ROW row = NULL;
	while((row = mysql_fetch_row(result)) != NULL)
		for(int i(atoi(row[1])); i > 0; i--)
			vec.push_back(row[0] ? row[0] : "");

	if(!vec.size())
	{
		mysql_free_result(result);
		return 0;
	}

	unordered_set <string> S(vec.begin(), vec.end());
	vector <string> res(3);

	if(S.size() == 1) res[0] = *S.begin();
	else if(S.size() == 2)
	{
		res[0] = *S.begin();
		res[1] = *(++S.begin());
		if(res[0] != f(vec)) swap(res[0], res[1]);
	}
	else
	{
		res[0] = f(vec);
		vector <string> vec_;
		for(int i{}; i < vec.size(); i++)
			if(vec[i] != res[0])
				vec_.emplace_back(vec[i]);
		res[1] = f(vec_);
		vector <string> vec__;
		for(int i{}; i < vec_.size(); i++)
			if(vec_[i] != res[1])
				vec__.push_back(vec_[i]);
		res[2] = f(vec__);
	}

	ll total(vec.size() * 25);
	if(result != NULL) mysql_free_result(result);
	for(int i{}; i < 3; i++)
	{
		if(!res[i].size()) continue;
		ll cur(total);
		if(i == 0) cur *= 0.70;
		if(i == 1) cur *= 0.20;
	        if(i == 2) cur *= 0.10;

		sprintf(query, "update uinfo set coin = coin + %lld, total_coin = total_coin + %lld where user_id = '%s'", cur, cur, res[i].c_str());
		mysql_real_query(conn, query, strlen(query));

		sprintf(query, "insert into content_log values('%s', 1, '%s가 코인 %lld 획득[복권 %d]', '%s의 코인 %d', now())", res[i].c_str(), res[i].c_str(), cur, i + 1, res[i].c_str(), getCoinInfo(conn, res[i]));
		mysql_real_query(conn, query, strlen(query));
		// 1등의 '행운아' 퀘스트 진척도 업데이트
		if(i == 0)
		{
			vector <string> vec;
			getProgress(conn, vec, res[0]);
			if(vec[7] == "-1") continue;
			ProgressUpdate(conn, vec);
		}
	}

	sprintf(query, "delete from lottery");
	mysql_real_query(conn, query, strlen(query));
}