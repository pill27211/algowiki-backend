#include <bits/stdc++.h>
#include "/usr/include/mysql/mysql.h"
using ll = long long;
using namespace std;

random_device rd;
mt19937 gen(rd());

int dateCheck()
{
    time_t start, end;
    struct tm user_stime;
    int tm_day, tm_hour, tm_min, tm_sec;
    double diff;
    int my_year(2024), my_month(1), my_day(2);


    user_stime.tm_year = my_year - 1900;
    user_stime.tm_mon = my_month - 1;
    user_stime.tm_mday = my_day;
    user_stime.tm_hour = 0;
    user_stime.tm_min = 0;
    user_stime.tm_sec = 0;
    user_stime.tm_isdst = 0;

    start = mktime(&user_stime);
    time(&end);

    diff = difftime(end, start);

    tm_day = diff / (60 * 60 * 24);
    diff = diff - (tm_day * 60 * 60 * 24);

    tm_hour = diff / (60 * 60);
    diff = diff - (tm_hour * 60 * 60);

    tm_min = diff / 60;
    diff = diff - (tm_min * 60);

    tm_sec = diff;

    return (tm_day % 3);
}
void fillExpTable(vector <ll>& exp_total)
{
	vector <ll> exp_a(30002);
	exp_a[1] = exp_total[1] = 10;

	for (int i(2); i < 10; i++)
	{
    		exp_a[i] = exp_a[i - 1] * 1.45;
    		exp_total[i] = exp_total[i - 1] + exp_a[i];
	}

	for (int i = 10; i < 101; i++)
	{
      		exp_a[i] = exp_a[i-1] + i / 2;
      		exp_total[i] = exp_total[i - 1] + exp_a[i];
   	}

	exp_a[101] = 3000;
   	exp_total[101] = exp_total[100] + 3000;

	for (int i(102); i < 30002; i++)
	{
    		exp_a[i] = exp_a[i - 1];
     		 exp_total[i] = exp_total[i - 1] + exp_a[i];
	}
}
void getUsers(MYSQL * conn, unordered_map <string, vector <int>>& users)
{
	char query[1001]{};
	sprintf(query, "select user_id from users");
	mysql_real_query(conn, query, strlen(query));

	MYSQL_RES * result = mysql_store_result(conn);
	MYSQL_ROW row = NULL;
	while((row = mysql_fetch_row(result)) != NULL)
		if(row[0])
			users[row[0]] = {};

	mysql_free_result(result);
}
int getLev(MYSQL * conn, vector <ll>& exp_total, string user_id)
{
	char query[1001]{};
	sprintf(query, "select acc_exp from uinfo where user_id = '%s'", user_id.c_str());
	mysql_real_query(conn, query, strlen(query));

	MYSQL_RES * result = mysql_store_result(conn);
	MYSQL_ROW row = mysql_fetch_row(result);

	ll exp(atoll(row[0]));
	mysql_free_result(result);

	int l(1), r(30000), res{};
	while(l <= r)
	{
		int mid(l + r >> 1);
		if(exp_total[mid] > exp) r = mid - 1;
		else
		{
			l = mid + 1;
			res = mid;
		}
	}
	return ++res;
}
void getProductList(MYSQL * conn, vector <int>& product_id_list, vector <ll>& exp_total, string user_id)
{
	int lev(getLev(conn, exp_total, user_id));
	vector < pair<int, int>> plist{ {6, 1}, {7, 1}, {8, 1}, {9, 1}, {10, 1}, {11, 1}, {12, 1}, {13, 25}, {14, 50}, {15, 75}, {18, 1}, {20, 1},
					{29, 1}, {30, 1}, {31, 1}, {32, 1}, {33, 1}, {34, 1}, {35, 1}, {36, 1}};

	// 테두리들 (음수)
	char query[1001]{};
	for(int i(-16); i >= -36; i--)
	{
		//이미 갖고 있는 테두리는 제외
		sprintf(query, "select count(*) from user_border where user_id = '%s' and border_id = %d", user_id.c_str(), -i);
		mysql_real_query(conn, query, strlen(query));

		MYSQL_RES * result = mysql_store_result(conn);
		MYSQL_ROW row = mysql_fetch_row(result);

		int flag(atoi(row[0]));
		mysql_free_result(result);

		if(!flag) plist.push_back({i, 1});
	}

	for(auto& [pid, pstand] : plist)
		if(lev >= pstand)
			product_id_list.push_back(pid);
}
void vSwap(vector <int>& vec)
{
	std::uniform_int_distribution<ll> swc(100, 200);
	std::uniform_int_distribution<ll> left(0, (int)vec.size() / 2);
	std::uniform_int_distribution<ll> right(vec.size() / 2, vec.size() - 1);

	for(int i(vec.size() * swc(gen)); i > 0; i--)
		swap(vec[left(gen)], vec[right(gen)]);
}
int lossTest(MYSQL * conn, string user_id)
{
	vector <int> vec(100);
	for(int i{}; i < 100; i++)
	    vec[i] = i % 3 ? 1 : 2;
	vSwap(vec);

	std::uniform_int_distribution<ll> ran(0, 99);
	int test(ran(gen));

	if(vec[test] == 1) return 0;

	char query[1001]{};
	sprintf(query, "insert into user_product_visibility values('%s', 1, 2, 1, 1, 1)", user_id.c_str());
	mysql_real_query(conn, query, strlen(query));
	return 1;
}

int getRandDiscountRate()
{
	vector <int> vec(36);
	iota(vec.begin(), vec.end(), 15);
	vSwap(vec);

	std::uniform_int_distribution<ll> ran(0, vec.size() - 1);
	return vec[ran(gen)];
}
int getSalePrice(MYSQL * conn, int pid)
{
	// 테두리는 2000
	if(pid < 0)
		return 2000 - 2000 * getRandDiscountRate() / 100;

	char query[1001]{};
	sprintf(query, "select price from product where product_id = %d", pid);
	mysql_real_query(conn, query, strlen(query));
		
	MYSQL_RES * result = mysql_store_result(conn);
	MYSQL_ROW row = mysql_fetch_row(result);
	int sale_price(atoi(row[0]));
	mysql_free_result(result);

	return sale_price - sale_price * getRandDiscountRate() / 100;
}
int getRandRemainCount()
{
	vector <int> vec(5);
	iota(vec.begin(), vec.end(), 1);
	vSwap(vec);

	std::uniform_int_distribution<ll> ran(0, vec.size() - 1);
	return vec[ran(gen)];
}
void ProductRandomInsert(MYSQL * conn, vector <int>& product_id_list, string user_id)
{
	std::uniform_int_distribution<ll> ran(0, product_id_list.size() - 1);
	vSwap(product_id_list);

	vector <bool> IsUse(product_id_list.size());
	char query[1001]{};
	for(int i(3); i > 0; i--)
	{
		if(lossTest(conn, user_id)) continue;
		int idx{};
		while(1)
		{
			idx = ran(gen);
			if(!IsUse[idx]) break;
		}

		int rcount(getRandRemainCount());
		if(product_id_list[idx] < 0) rcount = 1;

		sprintf(query, "insert into user_product_visibility values('%s', %d, 2, %d, 1, %d)", user_id.c_str(), product_id_list[idx], rcount, getSalePrice(conn, product_id_list[idx]));
		mysql_real_query(conn, query, strlen(query));
		IsUse[idx] = 1;
	}
}
int main()
{
	if(dateCheck()) return 0;

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

// 전처리 할거-----------------------------------------------------------------------------------------------
	// exp table init
	vector <ll> exp_total(30002);
	fillExpTable(exp_total);

	// user_product_visibility table init
	char query[1001]{};
	sprintf(query, "delete from user_product_visibility where vis_type = 2");
	mysql_real_query(conn, query, strlen(query));
//----------------------------------------------------------------------------------------------------------
	unordered_map <string, vector<int>> users; // 최종적으로 {유저명, 뽑힌 상품 목록} 의 형태

	// 먼저 모든 유저명을 담음
	getUsers(conn, users);

	// 다음으로, 유저별 해당 유저의 특가 상점에 등장할 수 있는 상품들을 뽑음
	for(auto& [user_id, product_id_list] : users)
		getProductList(conn, product_id_list, exp_total, user_id);

	// 이제 유저별로 특가 상점에 뜰 상품들 ㄱㄱ
	for(auto& [user_id, product_id_list] : users)
		ProductRandomInsert(conn, product_id_list, user_id);

	return 0;
}