#include "common_header.h"

struct stat_info
{
	int solution_id;
	int problem_id;
	string user_id;
	int stat_type;
	int time;
	int memory;
	int language;
	string date;
	int code_length;
};

void getList(MYSQL * conn, vector <stat_info>& list, int problem_id, int type)
{
	char query[1001]{};
	sprintf(query, "select * from problem_statistics where problem_id = %d and statistical_type = %d", problem_id, type);
	mysql_real_query(conn, query, strlen(query));

	MYSQL_RES * result = mysql_store_result(conn);
	if(result == NULL) return;

	MYSQL_ROW row = NULL;
	while((row = mysql_fetch_row(result)) != NULL)
		list.push_back({atoi(row[0]), atoi(row[1]), string(row[2]), atoi(row[3]), atoi(row[4]), atoi(row[5]), atoi(row[6]), string(row[7]), atoi(row[8])});

	mysql_free_result(result);
}
void delList(MYSQL * conn, int problem_id, int type)
{
	char query[1001]{};
	sprintf(query, "delete from problem_statistics where problem_id = %d and statistical_type = %d", problem_id, type);
	mysql_real_query(conn, query, strlen(query));
}

int questCheck(MYSQL * conn, char* user_id, int quest_id)
{
	char query[1001]{};
	sprintf(query, "select quest_rec_rewards from progress where user_id = '%s' and quest_id = %d", user_id, quest_id);
	mysql_real_query(conn, query, strlen(query));

	MYSQL_RES * result = mysql_store_result(conn);
	if(result == NULL) return 1;

	MYSQL_ROW row = mysql_fetch_row(result);
	int flag = atoi(row[0]);

	mysql_free_result(result);
	return flag;
}
int getStatCnt(MYSQL * conn, char* user_id, int type)
{
	char query[1001]{};
	sprintf(query, "select count(*) from problem_statistics where user_id = '%s' and statistical_type = %d", user_id, type);
	mysql_real_query(conn, query, strlen(query));

	MYSQL_RES * result = mysql_store_result(conn);
	if(result == NULL) return 0;

	MYSQL_ROW row = mysql_fetch_row(result);
	int cnt = atoi(row[0]);

	mysql_free_result(result);
	return cnt;
}
void questUpdate(MYSQL * conn, char* user_id, int quest_id, int cnt)
{
	char query[1001] = {};
	sprintf(query, "select quest_end_prog from quests where quest_id = %d", quest_id);
	mysql_real_query(conn, query, strlen(query));

	MYSQL_RES * result = mysql_store_result(conn);
	if(result == NULL) return;

	MYSQL_ROW row = mysql_fetch_row(result);
	int end_prog = atoi(row[0]);

	int new_user_prog = min(cnt, end_prog);
	int new_quest_sort_weight = new_user_prog == end_prog;

	sprintf(query, "update progress set user_prog = %d, quest_sort_weight = %d where user_id = '%s' and quest_id = %d", new_user_prog, new_quest_sort_weight, user_id, quest_id);
	mysql_real_query(conn, query, strlen(query));

	if(cnt <= end_prog)
	{
		sprintf(query, "insert into content_log values('%s', 8, '%s 의 퀘스트 %d 진척도 변화', '%s 의 %d 번 진척도 %d', now())", user_id, user_id, quest_id, user_id, quest_id, new_user_prog);
		mysql_real_query(conn, query, strlen(query));
	}
	mysql_free_result(result);
}
void main_stat_process(MYSQL* conn, int solution_id, int time, int memory)
{
	char query[1001]{};
	sprintf(query, "update solution set time = %d, memory = %d, result = 4 where solution_id = %d", time, memory, solution_id);
	mysql_real_query(conn, query, strlen(query));

	sprintf(query, "select * from solution where solution_id = %d", solution_id);
	mysql_real_query(conn, query, strlen(query));

	MYSQL_RES* result = mysql_store_result(conn);
	if (result == NULL) return;

	MYSQL_ROW row = mysql_fetch_row(result);
	if(string(row[2]) == "pill27211") return;


	// 정답이니 user_wa_problem 테이블에서 삭제 및 정답 제출 수 업데이트
	sprintf(query, "delete from user_wa_problem where user_id = '%s' and problem_id = %d", row[2], atoi(row[1]));
	mysql_real_query(conn, query, strlen(query));
	sprintf(query, "update users set solved = solved + 1 where user_id = '%s'", row[2]);
	mysql_real_query(conn, query, strlen(query));


	vector <int> qid[5]{ {0}, {86, 87, 88, 89}, {82, 83, 84, 85}, {78, 79, 80, 81}, {74, 75, 76, 77} };
	for (int i(1); i <= 4; i++)
	{
		vector <stat_info> list;
		getList(conn, list, atoi(row[1]), i); //atoi(row[1])번 문제의 명예의 전당에서 i번 타입에 들어있는 실제 레코드들을 뽑음
		delList(conn, atoi(row[1]), i); //atoi(row[1])번 문제의 명예의 전당에서 i번 타입에 들어있는 실제 레코드들을 일단 전부 지움
						//복잡하게 업데이트할 것 없이, 현재 보고 있는 solution을 고려하여 새롭게 목록을 뽑아넣기 위함

		int flag{}, idx{}, j(-1); // atoi(row[1])번 문제의 명예의 전당에서 i번 타입에 현재 유저가 이미 등재되어 있는지 여부
		for (auto& info : list)
			if (j++; info.user_id == string(row[2]))
				flag = 1, idx = j;

		if (flag) //등재되어 있다면 기존 등재 기록과 비교한다. 단, 명예의 전당 타입별로 뭐와 뭐를 비교해야 하는지 주의해야 함.
		{
			int flag2{};
			if (i == 1) flag2 = atoi(row[4]) < list[idx].time;
			else if (i == 2) flag2 = atoi(row[5]) < list[idx].memory;
			else if (i == 3) flag2 = atoi(row[13]) < list[idx].code_length;
			else flag2 = atoi(row[0]) < list[idx].solution_id;
			if (flag2) // 기존 등재된 기록과 비교했을 때 앞선다면 기존 기록을 지우고 지금 기록을 덧씌운다.
			{
				auto& [sid, pid, uid, type, time, mem, lang, date, cleng](list[idx]);
				sid = atoi(row[0]);
				pid = atoi(row[1]);
				uid = string(row[2]);
				type = i;
				time = atoi(row[4]);
				mem = atoi(row[5]);
				lang = atoi(row[8]);
				date = string(row[6]);
				cleng = atoi(row[13]);
			}
		}
		else // 처음 등장하는 유저의 제출이라면, list에 추가해준다.
		{
			list.push_back({ atoi(row[0]), atoi(row[1]), string(row[2]), i, atoi(row[4]), atoi(row[5]), atoi(row[8]), string(row[6]), atoi(row[13]) });
		}

		//이제 list를 명예의 전당 타입(i)에 맞게 정렬 후 새 목록을 뽑는다.
		if (i == 1)
			sort(list.begin(), list.end(), [&](stat_info& x, stat_info& y)
		{
			if (x.time == y.time)
				return x.solution_id < y.solution_id;
			return x.time < y.time;
		});
		else if (i == 2)
			sort(list.begin(), list.end(), [&](stat_info& x, stat_info& y)
		{
			if (x.memory == y.memory)
				return x.solution_id < y.solution_id;
			return x.memory < y.memory;
		});
		else if (i == 3)
			sort(list.begin(), list.end(), [&](stat_info& x, stat_info& y)
		{
			if (x.code_length == y.code_length)
				return x.solution_id < y.solution_id;
			return x.code_length < y.code_length;
		});
		else
			sort(list.begin(), list.end(), [&](stat_info& x, stat_info& y)
		{
			return x.solution_id < y.solution_id;
		});


		// 이제 앞에서부터 min(3, list.size())개만큼 레코드를 추가해주면 됨
		for (int i{}; i < min(3, (int)list.size()); i++)
		{
			auto [sid, pid, uid, type, time, mem, lang, date, cleng](list[i]);
			sprintf(query, "insert into problem_statistics values('%d', '%d', '%s', '%d', '%d', '%d', '%d', '%s', '%d')", sid, pid, uid.c_str(), type, time, mem, lang, date.c_str(), cleng);
			mysql_real_query(conn, query, strlen(query));
		}

		// 만약 list.size() > 3이면서 새로운 제출이 기존 명예의 전당에 변화를 준 경우,
		// 새로운 제출을 한 유저의 퀘스트 진행률은 ++, 기존 명예의 전당에서 떨어진 유저의 퀘스트 진행률은 --.

		// ++과 --는 problem_statistics 테이블의 statistical_type = x인 레코드 개수로 계산할 수 있다.
		// 단, 진행률을 감소시킬 때 이미 보상을 수령한 퀘스트는 pass 해야 한다.

		int cnt(getStatCnt(conn, row[2], i));
		for(auto qi : qid[i])
		{
			if(questCheck(conn, row[2], qi)) continue;
			questUpdate(conn, row[2], qi, cnt);
		}
	}

	mysql_free_result(result);
	return;
}