#include <bits/stdc++.h>
#include "/usr/include/mysql/mysql.h"
using namespace std;

void weekly_quest_init(MYSQL * conn)
{
	char query[1001]{};
	sprintf(query, "update progress set user_prog = 0, quest_rec_rewards = 0, quest_sort_weight = 0 where quest_class = 2");
	mysql_real_query(conn, query, strlen(query));
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
        	fprintf(stderr, "mysql_real_connect() ½ÇÆÐ: %s\n", mysql_error(conn));
        	mysql_close(conn);
        	return 1;
    	}

	//weekly_quest initialization
	weekly_quest_init(conn);

	
	mysql_close(conn);
}