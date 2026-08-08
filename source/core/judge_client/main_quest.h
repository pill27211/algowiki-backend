#include "common_header.h"





void general_progress_update(MYSQL * conn, vector <string>& vec)
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
int pioneer_check(MYSQL * conn, string user_id, int problem_id)
{
	char query[1001]{};
	sprintf(query, "select count(*) from problem_statistics where problem_id = %d and statistical_type = 4 and user_id = '%s'", problem_id, user_id.c_str());
	mysql_real_query(conn, query, strlen(query));

	MYSQL_RES * result = mysql_store_result(conn);
	if(result == NULL) return 0;
	MYSQL_ROW row = mysql_fetch_row(result);

	int res(atoi(row[0]));
	mysql_free_result(result);

	return res;
}

void main_quest_process(MYSQL * conn, vector <vector <string>> vec, int problem_id)
{
	//개척자 퀘스트 진행률 업데이트 가능 여부 확인 -> 스피더, 절약가, 숏코더 퀘스트는 ac 제출마다 검사해야 하므로 이곳에서 관리할 수 없음.
	//int pioneer_flag(pioneer_check(conn, vec[0][0].c_str(), problem_id));

	for(auto& cur : vec)
	{
		if(cur[1] == "45" || cur[1] == "67" || cur[1] == "68" || cur[1] == "69" || cur[1] == "70" || cur[1] == "71") general_progress_update(conn, cur);
		//if(pioneer_flag && (cur[1] == "74" || cur[1] == "75" || cur[1] == "76" || cur[1] == "77")) general_progress_update(conn, cur);
	}
}
