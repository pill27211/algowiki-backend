#include "common_header.h"




void fulfilling_week(MYSQL * conn, vector <string>& vec)
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

void weekly_quest_process(MYSQL * conn, vector <vector <string>> vec, int problem_id)
{
	for(auto& cur : vec)
	{
		if(cur[1] == "44") fulfilling_week(conn, cur);
	}

}
