# source/ — AlgoWiki 게임화 백엔드

프로젝트 전체는 오픈소스 [hustoj](https://github.com/zhblue/hustoj)(GPL-2.0) 기반이며,
여기에는 **hustoj 원본 코드는 포함하지 않고**, 그 위에 제가 새로 얹은 부분만 있습니다.

---

## 무엇이 들어 있나

제가 담당한 부분 중 주요 세 갈래 — **① 채점 파이프라인 연동, ② 주기별 크론 스케줄러/배치, ③ 백엔드 API·데이터 처리** — 만 담았습니다.

```
source/
├─ core/judge_client/                채점(judge) 연동 — hustoj 채점 클라이언트에 제가 얹은 부분
│  ├─ judge_client.gamification.patch   hustoj judge_client.cc 에 추가로 얹은 게임화 훅(diff)
│  │                                    · AC(result==4) 시 명예의전당 집계 main_stat_process(),
│  │                                      퀘스트 판정 ac_api_process() 훅 호출
│  │                                    · 태그별 통계 테이블 동기화 tag_table_synchronization()
│  ├─ update_stat.h                     명예의전당 — 문제별 속도/메모리/코드길이 top 집계
│  ├─ ac_api.h                          AC 시점 퀘스트 판정 진입점(ac_api_process)
│  ├─ common_header.h
│  └─ {daily,weekly,main,hidden}_quest.h  채점 시점에 평가되는 퀘스트 카테고리 로직(C)
│
├─ web/quest/                        퀘스트 시스템
│  ├─ quest_api/ac_category/*.h         퀘스트 달성 판정 로직(일일·주간·메인·히든, C)
│  ├─ scheduler/daily_scheduler.cpp     매일 00:00 일일 퀘스트 리셋 (cron)
│  ├─ scheduler/weekly_scheduler.cpp    매주 월 00:00 주간 퀘스트 리셋 (cron)
│  ├─ get_reward.php                    퀘스트 보상 수령 API
│  ├─ quest_db.php / quest_tab_db.php   퀘스트 조회·목록 데이터 처리
│  └─ profile_db.php                    프로필(레벨·코인·칭호) 데이터 처리
│
├─ web/inventory/                    재화·상점·인벤토리·아이템
│  ├─ shop_ajax.php / item_ajax.php     상점 구매 · 아이템 사용 백엔드 API
│  ├─ shop_db.php / inventory_db.php    상점·인벤토리 데이터 처리
│  ├─ coin_db.php / time_db.php         코인 · 시간복잡도 아이템 데이터 처리
│  ├─ shop_scheduler/daily_scheduler.cpp    매일 상품 라인업 로테이션 (cron, C++)
│  ├─ shop_scheduler/special_scheduler.cpp  스페셜 상품 로테이션 (cron, C++)
│  └─ script/dquest_{beg,nor,adv,all,tag}_reload.cpp
│                                       일일 퀘스트 리롤 아이템 — 난이도 티어별 재생성 스크립트(C++)
│
└─ web/lottery/
   └─ lottery_scheduler.cpp             복권 주간 추첨 (매주 월 cron, C++)
```

## 아키텍처 요약

성능·배치 성격(채점 연동, 퀘스트 판정, 상품/복권 추첨, 리롤)은 **C/C++**,
웹·AJAX·데이터 접근은 **PHP**로 분리하고, 주기 작업은 **cron**으로 돌렸습니다.

| 주기 | 작업 | 파일 |
|---|---|---|
| 채점 시(실시간) | 명예의전당 집계 · 퀘스트 AC 판정 · 태그 통계 | `core/judge_client/*` (+ `judge_client.gamification.patch`) |
| 매일 00:00 | 일일 퀘스트 리셋 | `web/quest/scheduler/daily_scheduler.cpp` |
| 매일 00:00 | 상점 라인업 로테이션 | `web/inventory/shop_scheduler/{daily,special}_scheduler.cpp` |
| 매주 월 00:00 | 주간 퀘스트 리셋 | `web/quest/scheduler/weekly_scheduler.cpp` |
| 매주 월 00:00 | 복권 추첨 | `web/lottery/lottery_scheduler.cpp` |

## 라이선스 · 출처

- 기반 엔진 **hustoj** 는 **GPL-2.0** 입니다 → 전문은 [`LICENSE`](LICENSE).
- 이 폴더의 코드는 hustoj 기반 프로젝트의 파생·연동물이므로 **GPL-2.0** 로 공개합니다.
- `judge_client.gamification.patch` 는 hustoj의 `core/judge_client/judge_client.cc`(GPL-2.0)에
  **게임화 훅 변경분(diff)만** 부분 발췌한 것이며, hustoj 원본 파일 자체는 이 저장소에 포함하지 않습니다.
- hustoj 저작권은 원저작자([zhblue](https://github.com/zhblue/hustoj))에게 있습니다.

## 유의사항

- 🔐 **비밀번호 마스킹** — 스케줄러/배치가 DB에 붙던 자리의 비밀번호는 모두 `"__REDACTED__"` 로 치환했습니다.
  (`server=localhost`, `user=hustoj`, `database=jol` 은 hustoj 기본값이라 그대로 둠)
- 🧩 **컴파일 산출물 제외** — 원래 함께 있던 ELF 실행 바이너리는 소스만 남기고 제외했습니다.
- 🌐 **인코딩** — 일부 C/C++ 파일의 주석은 **EUC-KR** 로 작성돼 있어, UTF-8 기준 뷰어에서 한글 주석이 깨져 보일 수 있습니다.
- 이 코드는 **당시 운영 코드의 스냅샷**으로, 그대로 빌드·실행되도록 정돈한 것이 아니라 기여 범위를 보이기 위한 아카이브입니다.

---

**© 2024 정필선([@pill27211](https://github.com/pill27211)).** 이 폴더의 코드는 GPL-2.0 로 배포됩니다.
