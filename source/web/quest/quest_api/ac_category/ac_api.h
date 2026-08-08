#include "common_header.h"
#include "daily_quest.h"
#include "weekly_quest.h"
#include "main_quest.h"
#include "hidden_quest.h"


int first_check(MYSQL* conn, string& user_id, int& problem_id, int solution_id)
{
	MYSQL_RES * result = NULL;
	MYSQL_ROW row = NULL;

	char query[1001] = {};

	sprintf(query, "select user_id, problem_id from solution where solution_id = %d", solution_id);
	mysql_real_query(conn, query, strlen(query));
	result = mysql_store_result(conn);

	if(result == NULL) return 1;
	row = mysql_fetch_row(result);

	user_id = row[0];
	problem_id = atoi(row[1]);

	sprintf(query, "select count(*) from accept where user_id = '%s' and problem_id = %d and solution_id != %d", user_id.c_str(), problem_id, solution_id);
	mysql_real_query(conn, query, strlen(query));

	if(result != NULL)  mysql_free_result(result);
	result = mysql_store_result(conn);

	if(result == NULL) return 1;

	row = mysql_fetch_row(result);
	int flag = strcmp(row[0], "1");

	if(result != NULL) mysql_free_result(result);
	return !flag;
}
int get_user_progress(MYSQL * conn, vector <vector <string>>& vec, string user_id)
{
	MYSQL_RES * result = NULL;
	MYSQL_ROW row = NULL;

	char query[1001] = {};

	sprintf(query, "select * from progress where user_id = '%s'", user_id.c_str());
	mysql_real_query(conn, query, strlen(query));
	result = mysql_store_result(conn);

	if(result == NULL) return 1;
	while((row = mysql_fetch_row(result)) != NULL)
	{
		vector <string> cur;
		for(int i{}; i < 9; i++)
			cur.push_back(row[i] ? string(row[i]) : "");
		vec.push_back(cur);
	}

	mysql_free_result(result);
	return 0;
}
void insert_accept(MYSQL * conn, string user_id, int problem_id, int solution_id)
{
	char query[1001] = {};

	sprintf(query, "insert into accept(problem_id, user_id, solution_id) values(%d, '%s', %d)", problem_id, user_id.c_str(), solution_id);
	mysql_real_query(conn, query, strlen(query));
}
void update_ac_rate(MYSQL * conn, string user_id, int problem_id)
{
	char query[1001] = {};
	MYSQL_RES * result = NULL;
	MYSQL_ROW row = NULL;

	sprintf(query, "select count(*) from solution where problem_id = %d and user_id = '%s' and result > 3", problem_id, user_id.c_str());
	mysql_real_query(conn, query, strlen(query));

	result = mysql_store_result(conn);
	if(result == NULL) return;

	row = mysql_fetch_row(result);
	if(row == NULL) return;

	int submit_cnt(atoi(row[0]) + 1);
	if(result != NULL) mysql_free_result(result);

	sprintf(query, "update pinfo set ac_person_count = ac_person_count + 1, first_ac_try = first_ac_try + %d where problem_id = %d", submit_cnt, problem_id);
	mysql_real_query(conn, query, strlen(query));
}

void ac_api_process(MYSQL * conn, int solution_id)
{
	string user_id{}; int problem_id{};
	if(first_check(conn, user_id, problem_id, solution_id)) // 잘못된 접근(쿼리) or 사용자가 첫 ac가 아님 (이전에 푼 기록이 있음)
		return;
	
	vector <vector <string>> vec; // 사용자의 progress를 2차원 벡터에 저장
	if(get_user_progress(conn, vec, user_id))
		return;

	insert_accept(conn, user_id, problem_id, solution_id); // 첫 ac 이므로 accept 테이블에 추가
	update_ac_rate(conn, user_id, problem_id); // 첫 ac 이므로 해당 문제에 대한 유저의 시도 횟수에 따른 정답률 업데이트




	/*
		이제 각 퀘스트를 순회하며 그에 맞는 처리를 진행.
		1차 분류 - 퀘스트 반영 전에 이미 진척도 100인지?
		2차 분류 - quest_class에 따른 (daliy, weekly, main, hidden).quest.h 로 분기
	*/

	for(int i(1); i <= 4; i++)
	{
		vector <vector <string>> cur;
		for(auto& temp : vec)
			if(temp[4] == to_string(i) && temp[2] != temp[3]) // 퀘스트 분류가 i이고 반영 전 진척도가 100이 아닌 경우, 기본적으로 처리 대상으로 봄
				cur.emplace_back(temp);


		if(i == 1) daliy_quest_process(conn, cur, problem_id);
		else if(i == 2) weekly_quest_process(conn, cur, problem_id);
		else if(i == 3) main_quest_process(conn, cur, problem_id);
		else if(i == 4) hidden_quest_process(conn, cur, problem_id);
	}
}