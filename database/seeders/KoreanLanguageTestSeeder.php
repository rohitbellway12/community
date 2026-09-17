<?php

namespace Database\Seeders;

use App\Models\AttemptAnswer;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\TestLevel;
use App\Models\TestQuestion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KoreanLanguageTestSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Clean up all tables
        AttemptAnswer::truncate();
        TestAttempt::truncate();
        TestQuestion::truncate();
        QuestionOption::truncate();
        Question::truncate();
        Test::truncate();
        TestLevel::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // 1. Create Test Levels in authentic Korean
        $level1 = TestLevel::create([
            'name' => '한국어 기초 능력 평가 (초급 / TOPIK I)',
            'slug' => 'topik-beginner-level',
            'description' => '한국어 기본 어휘, 한글 이해, 일상 인사말 및 기초 문법 평가',
            'status' => 'active',
        ]);

        $level2 = TestLevel::create([
            'name' => '한국어 실무 및 직무 어휘 평가 (실무 / EPS-TOPIK)',
            'slug' => 'eps-topik-workplace-level',
            'description' => '작업장 실무 한국어, 직장 생활 표현, 안전 수칙 및 도구 용어 평가',
            'status' => 'active',
        ]);

        // 2. TEST 1: EPS-TOPIK 한국어 기초 능력 평가 (제1차)
        // Active from today (2026-09-16) to tomorrow (2026-09-17)
        $test1 = Test::create([
            'test_level_id' => $level1->id,
            'title' => 'EPS-TOPIK 한국어 기초 능력 평가 (제1차)',
            'description' => 'Official online examination comprehensively evaluating basic Korean vocabulary, greetings, daily conversation, and basic grammar skills.',
            'instructions' => "1. This examination consists of 10 multiple-choice questions.\n2. Each question carries 1 mark for a total of 10 marks. The passing cutoff is 7 marks (70%) or higher.\n3. The examination time limit is 15 minutes. The countdown timer starts immediately upon test initiation.\n4. When the countdown timer reaches 00:00, all saved answers will be automatically submitted.\n5. Leaving the examination window or switching browser tabs will immediately auto-submit the exam to prevent cheating.",
            'duration_minutes' => 15,
            'total_marks' => 10,
            'passing_marks' => 7,
            'open_date' => '2026-09-16',
            'open_time' => '00:00:00',
            'close_date' => '2026-09-17',
            'close_time' => '23:59:59',
            'auto_open' => true,
            'max_attempts' => 1,
            'tab_switch_limit' => 1,
            'status' => 'published',
        ]);

        $test1QuestionsData = [
            [
                'question_text' => '다음 중 아침에 사람을 만났을 때 나누는 올바른 인사말은 무엇입니까?',
                'explanation' => '아침에 윗사람이나 동료를 만났을 때 공손하게 건네는 올바른 인사는 "안녕히 주무셨어요?" 또는 "안녕하세요?"입니다.',
                'options' => [
                    ['text' => '안녕히 주무셨어요?', 'label' => 'A', 'is_correct' => true],
                    ['text' => '안녕히 가세요.', 'label' => 'B', 'is_correct' => false],
                    ['text' => '처음 뵙겠습니다.', 'label' => 'C', 'is_correct' => false],
                    ['text' => '수고하셨습니다.', 'label' => 'D', 'is_correct' => false],
                ],
            ],
            [
                'question_text' => '다음 밑줄 친 단어의 반대 의미를 가진 말은 무엇입니까? [가방이 너무 "무겁습니다."]',
                'explanation' => '무게가 많이 나가는 "무겁다"의 반대말은 무게가 적게 나가는 "가볍다(가볍습니다)"입니다.',
                'options' => [
                    ['text' => '가볍습니다', 'label' => 'A', 'is_correct' => true],
                    ['text' => '큽니다', 'label' => 'B', 'is_correct' => false],
                    ['text' => '작습니다', 'label' => 'C', 'is_correct' => false],
                    ['text' => '비쌉니다', 'label' => 'D', 'is_correct' => false],
                ],
            ],
            [
                'question_text' => '빈칸에 들어갈 알맞은 조사를 고르십시오. [저는 사과_____ 바나나를 좋아합니다.]',
                'explanation' => '두 명사를 나란히 연결할 때 받침이 없는 명사 뒤에는 접속조사 "와"를 사용합니다 (사과와 바나나).',
                'options' => [
                    ['text' => '와', 'label' => 'A', 'is_correct' => true],
                    ['text' => '로', 'label' => 'B', 'is_correct' => false],
                    ['text' => '에', 'label' => 'C', 'is_correct' => false],
                    ['text' => '도', 'label' => 'D', 'is_correct' => false],
                ],
            ],
            [
                'question_text' => '다음 단어들과 가장 관계가 깊은 장소는 어디입니까? [의사, 간호사, 진료, 주사]',
                'explanation' => '의사와 간호사가 환자를 진료하고 치료하는 의료 기관은 "병원"입니다.',
                'options' => [
                    ['text' => '병원', 'label' => 'A', 'is_correct' => true],
                    ['text' => '은행', 'label' => 'B', 'is_correct' => false],
                    ['text' => '도서관', 'label' => 'C', 'is_correct' => false],
                    ['text' => '우체국', 'label' => 'D', 'is_correct' => false],
                ],
            ],
            [
                'question_text' => '빈칸에 들어갈 가장 알맞은 동사를 고르십시오. [오늘 날씨가 매우 춥습니다. 따뜻한 외투를 _____.]',
                'explanation' => '상의, 하의, 외투 등 옷을 몸에 걸칠 때 사용하는 표준 동사는 "입다(입으세요)"입니다.',
                'options' => [
                    ['text' => '입으세요', 'label' => 'A', 'is_correct' => true],
                    ['text' => '신으세요', 'label' => 'B', 'is_correct' => false],
                    ['text' => '쓰세요', 'label' => 'C', 'is_correct' => false],
                    ['text' => '끼세요', 'label' => 'D', 'is_correct' => false],
                ],
            ],
            [
                'question_text' => '다음 문장의 시제와 어울리는 바른 형태를 고르십시오. [어제 친구와 영화관에서 영화를 _____.]',
                'explanation' => '"어제"는 이미 지난 과거 시점이므로 동사의 과거형인 "봤습니다"가 올바른 표현입니다.',
                'options' => [
                    ['text' => '봤습니다', 'label' => 'A', 'is_correct' => true],
                    ['text' => '봅니다', 'label' => 'B', 'is_correct' => false],
                    ['text' => '보겠습니다', 'label' => 'C', 'is_correct' => false],
                    ['text' => '보고 싶습니다', 'label' => 'D', 'is_correct' => false],
                ],
            ],
            [
                'question_text' => '숫자 [15]를 순우리말로 바르게 읽은 것은 무엇입니까?',
                'explanation' => '숫자 15는 순우리말 기수사로 "열다섯"이라고 읽습니다.',
                'options' => [
                    ['text' => '열다섯', 'label' => 'A', 'is_correct' => true],
                    ['text' => '열둘', 'label' => 'B', 'is_correct' => false],
                    ['text' => '스물하나', 'label' => 'C', 'is_correct' => false],
                    ['text' => '서른', 'label' => 'D', 'is_correct' => false],
                ],
            ],
            [
                'question_text' => '대화의 빈칸에 들어갈 알맞은 답변을 고르십시오. [가: "도와주셔서 정말 감사합니다." / 나: "_____."]',
                'explanation' => '상대방의 감사 표현에 대해 겸손하게 응답하는 알맞은 정중한 표현은 "천만에요, 별말씀을요"입니다.',
                'options' => [
                    ['text' => '천만에요, 별말씀을요.', 'label' => 'A', 'is_correct' => true],
                    ['text' => '실례합니다.', 'label' => 'B', 'is_correct' => false],
                    ['text' => '안녕히 가세요.', 'label' => 'C', 'is_correct' => false],
                    ['text' => '죄송합니다.', 'label' => 'D', 'is_correct' => false],
                ],
            ],
            [
                'question_text' => '다음 설명에 해당하는 직업은 무엇입니까? [학교나 학원에서 학생들을 가르치는 사람]',
                'explanation' => '교육 기관에서 학생들에게 지식과 교양을 가르치는 전문 직업은 "선생님(교사)"입니다.',
                'options' => [
                    ['text' => '선생님', 'label' => 'A', 'is_correct' => true],
                    ['text' => '경찰관', 'label' => 'B', 'is_correct' => false],
                    ['text' => '요리사', 'label' => 'C', 'is_correct' => false],
                    ['text' => '운전기사', 'label' => 'D', 'is_correct' => false],
                ],
            ],
            [
                'question_text' => '빈칸에 들어갈 알맞은 위치 명사를 고르십시오. [필통과 연필이 책상 _____에 가지런히 놓여 있습니다.]',
                'explanation' => '물건이 바닥이나 가구의 표면 위에 얹혀 있는 상태를 가리키는 위치 명사는 "위"입니다.',
                'options' => [
                    ['text' => '위', 'label' => 'A', 'is_correct' => true],
                    ['text' => '안', 'label' => 'B', 'is_correct' => false],
                    ['text' => '속', 'label' => 'C', 'is_correct' => false],
                    ['text' => '사이', 'label' => 'D', 'is_correct' => false],
                ],
            ],
        ];

        foreach ($test1QuestionsData as $index => $qData) {
            $question = Question::create([
                'test_level_id' => $level1->id,
                'question_text' => $qData['question_text'],
                'question_type' => 'mcq',
                'marks' => 1,
                'explanation' => $qData['explanation'],
                'status' => 'active',
            ]);

            foreach ($qData['options'] as $opt) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $opt['text'],
                    'option_label' => $opt['label'],
                    'is_correct' => $opt['is_correct'],
                ]);
            }

            TestQuestion::create([
                'test_id' => $test1->id,
                'question_id' => $question->id,
                'question_order' => $index + 1,
            ]);
        }

        // 3. TEST 2: EPS-TOPIK 한국어 실무 및 산업 안전 평가 (제2차)
        // Active from today (2026-09-16) to tomorrow (2026-09-17)
        $test2 = Test::create([
            'test_level_id' => $level2->id,
            'title' => 'EPS-TOPIK 한국어 실무 및 산업 안전 평가 (제2차)',
            'description' => 'Official online examination assessing workplace safety rules, tool and equipment vocabulary, and workplace communication skills.',
            'instructions' => "1. This examination consists of 10 multiple-choice questions.\n2. Each question carries 1 mark for a total of 10 marks. The passing cutoff is 7 marks (70%) or higher.\n3. The examination time limit is 15 minutes. The countdown timer starts immediately upon test initiation.\n4. When the countdown timer reaches 00:00, all saved answers will be automatically submitted.\n5. Leaving the examination window or switching browser tabs will immediately auto-submit the exam to prevent cheating.",
            'duration_minutes' => 15,
            'total_marks' => 10,
            'passing_marks' => 7,
            'open_date' => '2026-09-16',
            'open_time' => '00:00:00',
            'close_date' => '2026-09-17',
            'close_time' => '23:59:59',
            'auto_open' => true,
            'max_attempts' => 1,
            'tab_switch_limit' => 1,
            'status' => 'published',
        ]);

        $test2QuestionsData = [
            [
                'question_text' => '공장이나 건설 현장에서 낙하물로부터 머리를 보호하기 위해 반드시 착용해야 하는 보호구는 무엇입니까?',
                'explanation' => '작업 현장에서 머리를 보호하기 위해 착용하는 필수 안전 보호구는 "안전모"입니다.',
                'options' => [
                    ['text' => '안전모', 'label' => 'A', 'is_correct' => true],
                    ['text' => '마스크', 'label' => 'B', 'is_correct' => false],
                    ['text' => '안전화', 'label' => 'C', 'is_correct' => false],
                    ['text' => '귀마개', 'label' => 'D', 'is_correct' => false],
                ],
            ],
            [
                'question_text' => '빈칸에 들어갈 알맞은 단어를 고르십시오. [작업이 모두 끝난 후에는 사용한 공구를 원래 자리에 잘 _____.]',
                'explanation' => '물건이나 도구를 흩어지지 않게 제자리에 정돈해 둘 때는 "정리하다(정리하세요)"를 씁니다.',
                'options' => [
                    ['text' => '정리하세요', 'label' => 'A', 'is_correct' => true],
                    ['text' => '버리세요', 'label' => 'B', 'is_correct' => false],
                    ['text' => '찢으세요', 'label' => 'C', 'is_correct' => false],
                    ['text' => '켜세요', 'label' => 'D', 'is_correct' => false],
                ],
            ],
            [
                'question_text' => '다음 중 시간 표현 [오후 2시 30분]을 바르게 읽은 것은 무엇입니까?',
                'explanation' => '시간의 "시"는 고유어 수사(두 시), "분"은 한자어 수사(삼십 분)로 읽습니다.',
                'options' => [
                    ['text' => '오후 두 시 삼십 분', 'label' => 'A', 'is_correct' => true],
                    ['text' => '오후 이 시 삼십 분', 'label' => 'B', 'is_correct' => false],
                    ['text' => '오후 둘 시 서른 분', 'label' => 'C', 'is_correct' => false],
                    ['text' => '오후 두 시 서른 초', 'label' => 'D', 'is_correct' => false],
                ],
            ],
            [
                'question_text' => '빈칸에 가장 알맞은 접속 부사를 고르십시오. [오늘은 밖에서 강한 비가 옵니다. _____ 외출할 때 우산을 꼭 챙기세요.]',
                'explanation' => '앞 문장이 이유/원인이 되고 뒷 문장이 그에 따른 결과/권유이므로 인과를 나타내는 "그래서"가 적절합니다.',
                'options' => [
                    ['text' => '그래서', 'label' => 'A', 'is_correct' => true],
                    ['text' => '그러나', 'label' => 'B', 'is_correct' => false],
                    ['text' => '그리고', 'label' => 'C', 'is_correct' => false],
                    ['text' => '하지만', 'label' => 'D', 'is_correct' => false],
                ],
            ],
            [
                'question_text' => '작업장에 부착된 [금연 (禁煙)] 표지판의 올바른 의미는 무엇입니까?',
                'explanation' => '"금연"은 지정된 구역에서 담배를 피우지 말라는 의미입니다.',
                'options' => [
                    ['text' => '담배를 피우지 마십시오.', 'label' => 'A', 'is_correct' => true],
                    ['text' => '사진을 촬영하지 마십시오.', 'label' => 'B', 'is_correct' => false],
                    ['text' => '차량을 주차하지 마십시오.', 'label' => 'C', 'is_correct' => false],
                    ['text' => '쓰레기를 버리지 마십시오.', 'label' => 'D', 'is_correct' => false],
                ],
            ],
            [
                'question_text' => '대화의 빈칸에 알맞은 요청 표현을 고르십시오. [반장님: "이 무거운 상자를 어디로 옮길까요?" / 공장장: "저기 1호 창고 안으로 _____."]',
                'explanation' => '물건을 장소로 이동시켜 달라는 요청에는 "옮기다"를 활용한 "옮겨 주세요"가 올바릅니다.',
                'options' => [
                    ['text' => '옮겨 주세요', 'label' => 'A', 'is_correct' => true],
                    ['text' => '사 오세요', 'label' => 'B', 'is_correct' => false],
                    ['text' => '먹어 보세요', 'label' => 'C', 'is_correct' => false],
                    ['text' => '닫아 주세요', 'label' => 'D', 'is_correct' => false],
                ],
            ],
            [
                'question_text' => '다음 단어의 뜻과 가장 일치하는 것은 무엇입니까? ["퇴근하다"]',
                'explanation' => '"퇴근하다"는 하루의 정해진 근무를 마치고 집이나 숙소로 돌아가는 것을 뜻합니다.',
                'options' => [
                    ['text' => '하루의 일을 마치고 집으로 돌아가다', 'label' => 'A', 'is_correct' => true],
                    ['text' => '회사에 출근하여 일을 시작하다', 'label' => 'B', 'is_correct' => false],
                    ['text' => '동료들과 점심 식사를 하러 가다', 'label' => 'C', 'is_correct' => false],
                    ['text' => '다른 새로운 직장을 구하러 가다', 'label' => 'D', 'is_correct' => false],
                ],
            ],
            [
                'question_text' => '빈칸에 알맞은 출발점 조사를 고르십시오. [저는 올해 네팔_____ 온 수레시라고 합니다.]',
                'explanation' => '출신지나 출발 지점을 나타낼 때 체언 뒤에 붙는 조사는 "에서"입니다 (네팔에서 온).',
                'options' => [
                    ['text' => '에서', 'label' => 'A', 'is_correct' => true],
                    ['text' => '에게', 'label' => 'B', 'is_correct' => false],
                    ['text' => '까지', 'label' => 'C', 'is_correct' => false],
                    ['text' => '마다', 'label' => 'D', 'is_correct' => false],
                ],
            ],
            [
                'question_text' => '식당에서 음식을 정중하게 주문할 때 알맞은 표현은 무엇입니까?',
                'explanation' => '식당에서 메뉴를 주문할 때는 메뉴 이름 뒤에 수량과 함께 "주세요"를 사용합니다.',
                'options' => [
                    ['text' => '여기 김치찌개 일 인분 주세요.', 'label' => 'A', 'is_correct' => true],
                    ['text' => '김치찌개를 먹지 마세요.', 'label' => 'B', 'is_correct' => false],
                    ['text' => '김치찌개가 없습니다.', 'label' => 'C', 'is_correct' => false],
                    ['text' => '어서 오십시오.', 'label' => 'D', 'is_correct' => false],
                ],
            ],
            [
                'question_text' => '다음 중 몸이 아프거나 다쳤을 때 방문하는 장소가 아닌 곳은 어디입니까?',
                'explanation' => '내과, 정형외과, 약국은 진료와 약 처방을 위한 의료 시설이며, "은행"은 예금과 금융 업무를 보는 곳입니다.',
                'options' => [
                    ['text' => '은행', 'label' => 'A', 'is_correct' => true],
                    ['text' => '내과', 'label' => 'B', 'is_correct' => false],
                    ['text' => '정형외과', 'label' => 'C', 'is_correct' => false],
                    ['text' => '약국', 'label' => 'D', 'is_correct' => false],
                ],
            ],
        ];

        foreach ($test2QuestionsData as $index => $qData) {
            $question = Question::create([
                'test_level_id' => $level2->id,
                'question_text' => $qData['question_text'],
                'question_type' => 'mcq',
                'marks' => 1,
                'explanation' => $qData['explanation'],
                'status' => 'active',
            ]);

            foreach ($qData['options'] as $opt) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $opt['text'],
                    'option_label' => $opt['label'],
                    'is_correct' => $opt['is_correct'],
                ]);
            }

            TestQuestion::create([
                'test_id' => $test2->id,
                'question_id' => $question->id,
                'question_order' => $index + 1,
            ]);
        }
    }
}
