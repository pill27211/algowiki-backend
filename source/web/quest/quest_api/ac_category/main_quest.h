#include "common_header.h"





void general_progress_update(MYSQL * conn, vector <string>& vec)
{
	int new_user_prog = min(stoi(vec[2]) + 1, stoi(vec[3]));
	int new_quest_sort_weight = (double)new_user_prog / stoi(vec[3]) * 100;

	char query[1001] = {};
	sprintf(query, "update progress set user_prog = %d, quest_sort_weight = %d where progress_id = %s", new_user_prog, new_quest_sort_weight, vec[5].c_str());
	mysql_real_query(conn, query, strlen(query));
}




void main_quest_process(MYSQL * conn, vector <vector <string>> vec, int problem_id)
{
	for(auto& cur : vec)
	{
		if(cur[1] == "45" || cur[1] == "67" || cur[1] == "68" || cur[1] == "69" || cur[1] == "70" || cur[1] == "71") general_progress_update(conn, cur);
	}

}
