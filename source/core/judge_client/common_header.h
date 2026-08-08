#ifndef __C_COMMON_HEADER__
#include <stdio.h>
#include <stdio.h>
#include <syslog.h>
#include <errno.h>
#include <fcntl.h>
#include <stdlib.h>
#include <string.h>
#include <dirent.h>
#include <unistd.h>
#include <time.h>
#include <stdarg.h>
#include <ctype.h>
#include <sys/wait.h>
#include <sys/ptrace.h>
#include <sys/types.h>
#include <sys/user.h>
#include <sys/syscall.h>
#include <sys/time.h>
#include <sys/resource.h>
#include <sys/signal.h>
//#include <sys/types.h>
#include <sys/stat.h>
#include <unistd.h>
#endif

#ifndef __CPP_COMMON_HEADER__
#include <bits/stdc++.h>
using ll = long long;
using ld = long double;
using namespace std;
#endif

#ifdef OJ_USE_MYSQL
	#include <mysql/mysql.h>
#endif



