<?php
return [
    'locations' => [
        'TAIPEI101' => [
            'name' => '台北 101 城市眺望',
            'description' => '透過 Morandi Journeys 的導引，體驗台北最具代表性的摩天大樓與周邊藝文街區。',
            'myMapsUrl' => 'https://www.google.com/maps/d/u/0/embed?mid=1YxY1Z_pilot_demo_map',
            'cover' => 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="1200" height="675" viewBox="0 0 1200 675"%3E%3Cdefs%3E%3ClinearGradient id="grad" x1="0%25" y1="0%25" x2="100%25" y2="100%25"%3E%3Cstop offset="0%25" stop-color="%238b5cf6"/%3E%3Cstop offset="100%25" stop-color="%233c82f6"/%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width="1200" height="675" fill="url(%23grad)"/%3E%3Ctext x="50%25" y="50%25" font-size="64" fill="white" text-anchor="middle" dominant-baseline="middle" font-family="%27Noto Sans TC%27, sans-serif"%3EGuide Magnets DEMO%3C/text%3E%3C/svg%3E',
            'highlights' => [
                '以 3 小時導覽掌握摩天大樓內外部亮點',
                '串聯信義商圈與四四南村的步行路線建議',
                '提供日夜兩段式的拍照景點與推薦視角',
            ],
            'diaries' => [
                [
                    'id' => 'arrival',
                    'title' => 'Day 1 — 抵達信義計畫區',
                    'createdAt' => '2024-05-01 09:30:00',
                    'content' => '<p>從台北捷運市政府站出發，慢步前往台北 101。沿途可以在象山觀景台補充咖啡，俯瞰整個信義商圈。</p><p>下午安排參觀觀景台與世界級藝術展覽，傍晚則前往四四南村，享受寧靜的眷村氛圍。</p>',
                ],
                [
                    'id' => 'nightscape',
                    'title' => 'Day 1 — 夜景巡禮',
                    'createdAt' => '2024-05-01 19:00:00',
                    'content' => '<p>夜幕低垂後，可前往台北 101 觀景台 91 樓戶外平台，360 度感受城市燈火。建議攜帶薄外套以應付高空風勢。</p><p>下樓後走訪信義香堤大道，拍攝街頭藝術與水舞表演，最後在 A13 屋頂酒吧品飲結束行程。</p>',
                ],
            ],
            'photos' => [
                [
                    'id' => 'sunset',
                    'title' => '暮色下的 101',
                    'description' => '從象山步道望向台北 101 的經典角度，晚霞染出橘紫漸層。',
                    'image' => 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="900" height="600" viewBox="0 0 900 600"%3E%3Crect width="900" height="600" fill="%23fde68a"/%3E%3Cpath d="M450 80 L520 460 L380 460 Z" fill="%239256d4"/%3E%3Ccircle cx="720" cy="140" r="80" fill="%23fb7185" opacity="0.8"/%3E%3Ctext x="50%25" y="92%25" font-size="40" text-anchor="middle" fill="%2352566d" font-family="%27Noto Sans TC%27, sans-serif"%3ETaipei Skyline%3C/text%3E%3C/svg%3E',
                    'attribution' => 'Morandi Journeys Demo 團隊攝製',
                ],
                [
                    'id' => 'lane',
                    'title' => '四四南村巷弄',
                    'description' => '被保留的老屋與新建築並存，呈現信義區獨特的時間層次。',
                    'image' => 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="900" height="600" viewBox="0 0 900 600"%3E%3Crect width="900" height="600" fill="%23bbf7d0"/%3E%3Crect x="120" y="180" width="220" height="280" fill="%233f6212" opacity="0.75"/%3E%3Crect x="360" y="150" width="260" height="320" fill="%2365a30d" opacity="0.75"/%3E%3Crect x="660" y="200" width="140" height="260" fill="%2393c5fd" opacity="0.9"/%3E%3Ctext x="50%25" y="88%25" font-size="40" text-anchor="middle" fill="%2322561b" font-family="%27Noto Sans TC%27, sans-serif"%3EVillage Contrast%3C/text%3E%3C/svg%3E',
                    'attribution' => 'Morandi Journeys Demo 團隊攝製',
                ],
            ],
            'videos' => [
                [
                    'id' => 'overview',
                    'title' => '3 分鐘導覽精華',
                    'description' => '以動畫與空拍畫面快速認識台北 101 與周邊動線。',
                    'type' => 'youtube',
                    'youtubeId' => 'dQw4w9WgXcQ',
                ],
            ],
        ],
    ],
];
