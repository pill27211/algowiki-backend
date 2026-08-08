#include "common_header.h"




void attendance_check(MYSQL * conn, vector <string>& vec)
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

	//consistency
	sprintf(query, "select user_prog, progress_id from progress where user_id = '%s' and quest_id = 54", vec[0].c_str());
	mysql_real_query(conn, query, strlen(query));

	MYSQL_RES * result = mysql_store_result(conn);
	if(result == NULL) return;
	MYSQL_ROW row = mysql_fetch_row(result);
	if(row == NULL) return;

	new_user_prog = min(atoi(row[0]) + 1, 7);
	new_quest_sort_weight = new_user_prog == stoi(vec[3]);


	if(atoi(row[0]) < 7)
	{
		sprintf(query, "insert into content_log values('%s', 8, '%s 의 퀘스트 %d 진척도 변화', '%s 의 %d 번 진척도 %d', now())", vec[0].c_str(), vec[0].c_str(), 54, vec[0].c_str(), 54, new_user_prog);
		mysql_real_query(conn, query, strlen(query));
	}

	sprintf(query, "update progress set user_prog = %d, quest_sort_weight = %d where progress_id = %s", new_user_prog, new_quest_sort_weight, row[1]);
	mysql_real_query(conn, query, strlen(query));

	if(result != NULL) mysql_free_result(result);
}
void random_tag_defense(MYSQL * conn, vector <string>& vec, int problem_id)
{
	//사용자가 퀘스트로 할당받은 태그가, 푼 문제의 알고리즘 분류 중 하나에 속하는지?
	char query[1001] = {};
	sprintf(query, "select source from problem where problem_id = %d", problem_id);
	mysql_real_query(conn, query, strlen(query));

	MYSQL_RES * result = mysql_store_result(conn);
	if(result == NULL) return;

	MYSQL_ROW row = mysql_fetch_row(result);
	if(row == NULL) return;

	int flag{};
	for(int i{}, j{}, l(strlen(row[0])); i < l; i++)
		if(row[0][i] == ' ' || i == l - 1)
		{
			string sub{};
			if(row[0][i] == ' ') sub = string(row[0] + j, row[0] + i), j = i + 1;
			else sub = string(row[0] + j, row[0] + i + 1);
			flag += sub == vec[8];
		}
	
	if(!flag) return; // 랜덤 태그 디펜스로 받은 태그의 문제를 풀지 않음.

	int new_user_prog = min(stoi(vec[2]) + 1, stoi(vec[3]));
	int new_quest_sort_weight = new_user_prog == stoi(vec[3]);

	if(stoi(vec[2]) < stoi(vec[3]))
	{
		sprintf(query, "insert into content_log values('%s', 8, '%s 의 퀘스트 %s 진척도 변화', '%s 의 %s 번 진척도 %d', now())", vec[0].c_str(), vec[0].c_str(), vec[1].c_str(), vec[0].c_str(), vec[1].c_str(), new_user_prog);
		mysql_real_query(conn, query, strlen(query));
	}

	sprintf(query, "update progress set user_prog = %d, quest_sort_weight = %d where progress_id = %s", new_user_prog, new_quest_sort_weight, vec[5].c_str());
	mysql_real_query(conn, query, strlen(query));

	mysql_free_result(result);
}
void random_diff_defense(MYSQL * conn, vector <string>& vec, int problem_id)
{
	if(problem_id != stoi(vec[8])) return; // 랜덤 난이도 디펜스로 받은 문제를 풀지 않음.

	int new_user_prog = min(stoi(vec[2]) + 1, stoi(vec[3]));
	int new_quest_sort_weight = new_user_prog == stoi(vec[3]);
	char query[1001]{};

	if(stoi(vec[2]) < stoi(vec[3]))
	{
		sprintf(query, "insert into content_log values('%s', 8, '%s 의 퀘스트 %s 진척도 변화', '%s 의 %s 번 진척도 %d', now())", vec[0].c_str(), vec[0].c_str(), vec[1].c_str(), vec[0].c_str(), vec[1].c_str(), new_user_prog);
		mysql_real_query(conn, query, strlen(query));
	}

	sprintf(query, "update progress set user_prog = %d, quest_sort_weight = %d where progress_id = %s", new_user_prog, new_quest_sort_weight, vec[5].c_str());
	mysql_real_query(conn, query, strlen(query));
}


void daliy_quest_process(MYSQL * conn, vector <vector <string>> vec, int problem_id)
{
	for(auto& cur : vec)
	{
		if(cur[1] == "23") random_tag_defense(conn, cur, problem_id);
		else if(cur[1] == "48") attendance_check(conn, cur);
		else if(cur[1] == "64" || cur[1] == "65" || cur[1] == "66") random_diff_defense(conn, cur, problem_id);
	}
}
