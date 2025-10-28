<?php
return [
    'locations' => [
        'TAIPEI101' => [
            'name' => '台北 101 城市眺望',
            'tagline' => '眺望信義計畫區天際線，細細品味摩天大樓內外的步行路線。',
            'description' => '行程採用最新旅遊資訊規畫，整合日誌、相片與影音花絮，完整呈現信義區的都會魅力。所有內容皆由編輯團隊定期維護更新。',
            'mapUrl' => 'https://www.google.com/maps/place/Taipei+101,+No.+7%E4%BF%A1%E7%BE%A9%E8%B7%AF%E4%BA%94%E6%AE%B5%E4%BF%A1%E7%BE%A9%E5%8D%80%E8%87%BA%E5%8C%97%E5%B8%82110/@25.0339808,121.561964,17z/data=!3m1!4b1!4m6!3m5!1s0x3442abb6da9c9e1f:0x1206bcf082fd10a6!8m2!3d25.033976!4d121.5645389!16zL20vMDFjeTZ5?entry=ttu&g_ep=EgoyMDI1MTAyMi4wIKXMDSoASAFQAw%3D%3D',
            'cover' => 'assets/images/v2/taipei-cover.svg',
            'highlights' => [
                '以步行串聯象山、台北 101 觀景台與四四南村。',
                '提供白天與夜景兩階段的拍攝建議。',
                '可在地圖中標註自行延伸的景點。',
            ],
            'diaries' => [
                [
                    'id' => 'arrival',
                    'title' => 'Day 1 — 抵達信義',
                    'createdAt' => '2024-05-01 09:30:00',
                    'content' => '<p>搭乘捷運抵達市政府站後，沿著香堤大道慢步前往台北 101。沿途可安排星巴克旗艦店補給，並預留 30 分鐘於象山觀景台拍攝日景。</p><p>午餐建議前往四四南村的聚落市集，品嚐結合在地與創意的料理。</p>',
                ],
                [
                    'id' => 'night',
                    'title' => 'Day 1 — 夜景巡禮',
                    'createdAt' => '2024-05-01 19:15:00',
                    'content' => '<p>傍晚進入 89 樓觀景台，欣賞金色時刻的城市燈光。建議攜帶廣角鏡頭以捕捉完整天際線。</p><p>結束後可到信義 A13 屋頂酒吧，搭配藍調爵士樂收尾。</p>',
                ],
            ],
            'photos' => [
                [
                    'id' => 'sunset',
                    'title' => '暮色下的 101',
                    'description' => '在象山步道高處拍攝，利用暮色與建築燈光交織的層次。',
                    'image' => 'assets/images/v2/taipei-sunset.svg',
                    'attribution' => 'Guide Magnets 內部攝影',
                ],
                [
                    'id' => 'village',
                    'title' => '四四南村巷弄',
                    'description' => '保留老屋與現代建築的對比，適合安排黃昏散步行程。',
                    'image' => 'assets/images/v2/taipei-lane.svg',
                    'attribution' => 'Guide Magnets 內部攝影',
                ],
            ],
            'videos' => [
                [
                    'id' => 'city-preview',
                    'title' => '3 分鐘城市導覽動畫',
                    'description' => '以沉浸式動畫呈現導覽重點，行前即可掌握整段旅程節奏。',
                    'type' => 'inlineSvg',
                    'svg' => <<<"SVG"
<svg xmlns="http://www.w3.org/2000/svg" width="960" height="540" viewBox="0 0 960 540">
  <defs>
    <linearGradient id="bg" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#1d4ed8" />
      <stop offset="100%" stop-color="#60a5fa" />
    </linearGradient>
  </defs>
  <rect width="960" height="540" fill="url(#bg)" rx="32" />
  <circle cx="200" cy="160" r="120" fill="rgba(255,255,255,0.16)">
    <animate attributeName="r" values="90;120;90" dur="8s" repeatCount="indefinite" />
  </circle>
  <circle cx="740" cy="380" r="140" fill="rgba(15,23,42,0.18)">
    <animate attributeName="opacity" values="0.12;0.32;0.12" dur="6s" repeatCount="indefinite" />
  </circle>
  <polygon points="420,190 660,270 420,350" fill="rgba(255,255,255,0.92)">
    <animateTransform attributeName="transform" type="scale" values="1;1.05;1" dur="3.6s" repeatCount="indefinite" additive="sum" origin="520 270" />
  </polygon>
  <rect x="360" y="160" width="48" height="220" rx="16" fill="rgba(255,255,255,0.9)">
    <animate attributeName="y" values="150;160;150" dur="4s" repeatCount="indefinite" />
  </rect>
  <text x="50%" y="86%" font-family="'Noto Sans TC', sans-serif" font-size="42" text-anchor="middle" fill="rgba(255,255,255,0.92)">
    台北 101 城市導覽預覽
  </text>
</svg>
SVG,
                ],
            ],
        ],
        'TAINANOLDTOWN' => [
            'name' => '台南老城光影',
            'tagline' => '漫步府城巷弄，蒐集每盞花燈與古廟的故事。',
            'description' => '以老城區為核心，安排步行與巷弄美食，包含神農街花燈、台南州廳與各式古廟。',
            'mapUrl' => 'https://www.google.com/maps/search/%E5%8F%B0%E5%8D%97%E8%80%81%E5%9F%8E%E5%85%89%E5%BD%B1/@22.9922808,120.1656237,14z/data=!3m1!4b1?entry=ttu&g_ep=EgoyMDI1MTAyMi4wIKXMDSoASAFQAw%3D%3D',
            'cover' => 'assets/images/v2/tainan-cover.svg',
            'highlights' => [
                '夜間行程搭配花燈展演與老屋咖啡館。',
                '白日可安排赤崁樓、孔廟等文化巡禮。',
                '提供市集小吃與伴手禮購買建議。',
            ],
            'diaries' => [
                [
                    'id' => 'lantern-walk',
                    'title' => 'Day 1 — 花燈慢遊',
                    'createdAt' => '2024-04-12 18:30:00',
                    'content' => '<p>傍晚自林百貨附近出發，沿著中正路前往神農街。沿途可停留於特色小店體驗手作工作坊。</p><p>夜晚的神農街掛滿多層次燈籠，建議攜帶腳架以長時間曝光紀錄光影。</p>',
                ],
                [
                    'id' => 'temple-morning',
                    'title' => 'Day 2 — 古廟晨走',
                    'createdAt' => '2024-04-13 07:45:00',
                    'content' => '<p>早晨漫步孔廟園區，感受書香氣息。可預約導覽了解建築細節。</p><p>後續前往赤崁樓，於周邊品嚐牛肉湯，完成府城早餐體驗。</p>',
                ],
            ],
            'photos' => [
                [
                    'id' => 'lanterns',
                    'title' => '神農街花燈',
                    'description' => '巷弄間懸掛的多彩燈籠，隨風擺動形成絢麗光影。',
                    'image' => 'assets/images/v2/tainan-lanterns.svg',
                    'attribution' => 'Guide Magnets 內部攝影',
                ],
                [
                    'id' => 'temple',
                    'title' => '府城古廟',
                    'description' => '晨光照耀紅磚與黃瓦，展現古都莊嚴沉穩的氣氛。',
                    'image' => 'assets/images/v2/tainan-temple.svg',
                    'attribution' => 'Guide Magnets 內部攝影',
                ],
            ],
            'videos' => [
                [
                    'id' => 'lantern-showcase',
                    'title' => '花燈步行路線導覽',
                    'description' => '透過動態光影呈現夜間花燈的色彩層次，走訪前便能掌握行程亮點。',
                    'type' => 'inlineSvg',
                    'svg' => <<<"SVG"
<svg xmlns="http://www.w3.org/2000/svg" width="960" height="540" viewBox="0 0 960 540">
  <rect width="960" height="540" fill="#f97316" rx="32" />
  <g>
    <ellipse cx="240" cy="220" rx="70" ry="100" fill="#fcd34d" opacity="0.85">
      <animate attributeName="opacity" values="0.6;0.95;0.6" dur="4s" repeatCount="indefinite" />
    </ellipse>
    <ellipse cx="480" cy="180" rx="90" ry="120" fill="#fef08a" opacity="0.85">
      <animate attributeName="ry" values="110;120;110" dur="6s" repeatCount="indefinite" />
    </ellipse>
    <ellipse cx="720" cy="240" rx="70" ry="110" fill="#fbbf24" opacity="0.85">
      <animate attributeName="rx" values="60;80;60" dur="5s" repeatCount="indefinite" />
    </ellipse>
  </g>
  <g stroke="#7c2d12" stroke-width="14" stroke-linecap="round">
    <line x1="240" y1="120" x2="240" y2="180">
      <animate attributeName="y2" values="160;190;160" dur="4s" repeatCount="indefinite" />
    </line>
    <line x1="480" y1="90" x2="480" y2="160">
      <animate attributeName="y2" values="140;180;140" dur="6s" repeatCount="indefinite" />
    </line>
    <line x1="720" y1="150" x2="720" y2="210">
      <animate attributeName="y2" values="200;230;200" dur="5s" repeatCount="indefinite" />
    </line>
  </g>
  <text x="50%" y="86%" font-family="'Noto Sans TC', sans-serif" font-size="42" text-anchor="middle" fill="#7c2d12">
    神農街花燈動畫預覽
  </text>
</svg>
SVG,
                ],
            ],
        ],
    ],
];
