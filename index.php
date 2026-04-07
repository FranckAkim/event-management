<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>CelebrateHub — Where Joy Becomes Legendary</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,600;1,300;1,500&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet" />
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        :root {
            --rose: #ff6b9d;
            --peach: #ff9a7c;
            --lemon: #ffe66d;
            --mint: #5ce6c8;
            --sky: #64c8ff;
            --lavender: #b8a0f8;
            --coral: #ff7d6b;
            --cream: #fffbf5;
            --sand: #fef3e2;
            --text: #2c1810;
            --text2: rgba(44, 24, 16, .62);
            --glass: rgba(255, 255, 255, .55);
            --gb: rgba(255, 255, 255, .75);
        }

        html {
            scroll-behavior: smooth
        }

        body {
            background: var(--sand);
            color: var(--text);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 300;
            overflow-x: hidden;
            cursor: none
        }

        /* cursor */
        #cur {
            position: fixed;
            width: 13px;
            height: 13px;
            border-radius: 50%;
            background: var(--rose);
            transform: translate(-50%, -50%);
            pointer-events: none;
            z-index: 9999;
            transition: transform .15s
        }

        #curR {
            position: fixed;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 2px solid rgba(255, 107, 157, .45);
            transform: translate(-50%, -50%);
            pointer-events: none;
            z-index: 9998;
            transition: width .22s, height .22s, border-color .22s
        }

        /* ─── SLIDESHOW ─────────────────────────── */
        #ss {
            position: fixed;
            inset: 0;
            z-index: 0
        }

        .sl {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            opacity: 0;
            transition: opacity 1.6s ease-in-out;
            transform: scale(1.08)
        }

        .sl.on {
            opacity: 1;
            animation: kb 7s ease-in-out forwards
        }

        .sl.out {
            opacity: 0
        }

        @keyframes kb {
            0% {
                transform: scale(1.08)
            }

            100% {
                transform: scale(1.0)
            }
        }

        /* bright warm overlay — keeps photos vibrant + text legible */
        #ssOv {
            position: fixed;
            inset: 0;
            z-index: 1;
            pointer-events: none;
            background:
                linear-gradient(180deg, rgba(255, 251, 245, .82) 0%, rgba(255, 251, 245, .28) 25%, rgba(255, 251, 245, .22) 75%, rgba(255, 251, 245, .88) 100%),
                linear-gradient(135deg, rgba(255, 214, 180, .22) 0%, rgba(200, 230, 255, .15) 100%);
        }

        /* dots */
        #dots {
            position: fixed;
            bottom: 26px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 50;
            display: flex;
            gap: 8px;
            align-items: center
        }

        .dot {
            width: 7px;
            height: 7px;
            border-radius: 4px;
            background: rgba(255, 255, 255, .5);
            border: 1.5px solid rgba(255, 255, 255, .9);
            transition: .35s;
            cursor: pointer
        }

        .dot.on {
            width: 24px;
            background: var(--rose);
            border-color: var(--rose)
        }

        /* canvas */
        #cv {
            position: fixed;
            inset: 0;
            z-index: 2;
            pointer-events: none
        }

        /* ─── NAV ─────────────────────────────── */
        nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            padding: 20px 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(255, 251, 245, .72);
            backdrop-filter: blur(22px);
            border-bottom: 1px solid rgba(255, 255, 255, .6);
        }

        .logo {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.7rem;
            font-weight: 600;
            color: var(--rose);
            text-decoration: none;
            letter-spacing: .02em
        }

        .logo b {
            color: var(--peach);
            font-style: italic;
            font-weight: 300
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 34px;
            align-items: center
        }

        nav a {
            color: var(--text);
            opacity: .65;
            text-decoration: none;
            font-size: .84rem;
            letter-spacing: .07em;
            text-transform: uppercase;
            transition: opacity .2s, color .2s
        }

        nav a:hover {
            opacity: 1;
            color: var(--rose)
        }

        .nCta {
            background: linear-gradient(135deg, var(--rose), var(--peach)) !important;
            color: #fff !important;
            opacity: 1 !important;
            padding: 10px 26px;
            border-radius: 40px;
            font-weight: 500 !important;
            box-shadow: 0 4px 20px rgba(255, 107, 157, .38);
            transition: transform .2s, box-shadow .2s !important
        }

        .nCta:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(255, 107, 157, .55) !important
        }

        /* ─── HERO ──────────────────────────── */
        .hero {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 120px 24px 90px
        }

        .eyebrow {
            font-size: .7rem;
            letter-spacing: .28em;
            text-transform: uppercase;
            color: var(--rose);
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: fu .8s ease both;
            font-weight: 500
        }

        .eyebrow::before,
        .eyebrow::after {
            content: '';
            flex: 1;
            max-width: 44px;
            height: 1.5px;
            background: linear-gradient(90deg, transparent, var(--rose))
        }

        .eyebrow::after {
            background: linear-gradient(90deg, var(--rose), transparent)
        }

        h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(3.2rem, 9vw, 7.8rem);
            font-weight: 300;
            line-height: 1.04;
            animation: fu .8s .08s ease both;
            text-shadow: 0 2px 30px rgba(255, 255, 255, .7)
        }

        h1 em {
            font-style: italic;
            display: block;
            background: linear-gradient(135deg, var(--rose) 0%, var(--peach) 40%, var(--lemon) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text
        }

        /* pills */
        .pills {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            margin: 30px 0 34px;
            animation: fu .8s .16s ease both
        }

        .pill {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 9px 20px;
            border-radius: 50px;
            font-size: .84rem;
            font-weight: 400;
            backdrop-filter: blur(14px);
            border: 1.5px solid rgba(255, 255, 255, .7);
            background: rgba(255, 255, 255, .48);
            box-shadow: 0 2px 14px rgba(0, 0, 0, .06);
            transition: .25s;
            cursor: none;
            letter-spacing: .02em
        }

        .pill:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 26px rgba(0, 0, 0, .1)
        }

        .pill:nth-child(1) {
            border-color: rgba(255, 107, 157, .45);
            background: rgba(255, 107, 157, .1)
        }

        .pill:nth-child(2) {
            border-color: rgba(255, 154, 124, .45);
            background: rgba(255, 154, 124, .1)
        }

        .pill:nth-child(3) {
            border-color: rgba(184, 160, 248, .5);
            background: rgba(184, 160, 248, .12)
        }

        .pill:nth-child(4) {
            border-color: rgba(100, 200, 255, .5);
            background: rgba(100, 200, 255, .12)
        }

        .pill:nth-child(5) {
            border-color: rgba(92, 230, 200, .5);
            background: rgba(92, 230, 200, .12)
        }

        .pill em {
            font-size: 1rem;
            font-style: normal
        }

        .sub {
            max-width: 530px;
            font-size: 1.06rem;
            line-height: 1.88;
            color: var(--text2);
            margin: 0 auto 46px;
            animation: fu .8s .24s ease both;
            text-shadow: 0 1px 10px rgba(255, 255, 255, .9)
        }

        .btns {
            display: flex;
            gap: 13px;
            justify-content: center;
            flex-wrap: wrap;
            animation: fu .8s .32s ease both
        }

        .bPri {
            background: linear-gradient(135deg, var(--rose), var(--coral));
            color: #fff;
            padding: 16px 40px;
            border-radius: 50px;
            font-size: .94rem;
            font-weight: 500;
            letter-spacing: .05em;
            text-decoration: none;
            box-shadow: 0 6px 26px rgba(255, 107, 157, .42);
            transition: .25s;
            cursor: none
        }

        .bPri:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 40px rgba(255, 107, 157, .58)
        }

        .bGhost {
            border: 2px solid rgba(44, 24, 16, .2);
            color: var(--text);
            padding: 14px 38px;
            border-radius: 50px;
            font-size: .94rem;
            letter-spacing: .05em;
            text-decoration: none;
            background: rgba(255, 255, 255, .42);
            backdrop-filter: blur(10px);
            transition: .22s;
            cursor: none
        }

        .bGhost:hover {
            border-color: var(--rose);
            color: var(--rose);
            background: rgba(255, 107, 157, .06)
        }

        /* live counter */
        .live {
            margin-top: 58px;
            display: flex;
            align-items: center;
            gap: 18px;
            background: var(--glass);
            border: 1.5px solid rgba(255, 255, 255, .8);
            border-radius: 20px;
            padding: 17px 30px;
            backdrop-filter: blur(22px);
            box-shadow: 0 8px 40px rgba(0, 0, 0, .07);
            animation: fu .8s .4s ease both
        }

        .ldot {
            width: 9px;
            height: 9px;
            background: var(--rose);
            border-radius: 50%;
            animation: lp 1.6s ease-in-out infinite;
            box-shadow: 0 0 0 0 rgba(255, 107, 157, .5)
        }

        @keyframes lp {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 107, 157, .5)
            }

            70% {
                box-shadow: 0 0 0 9px rgba(255, 107, 157, 0)
            }

            100% {
                box-shadow: 0 0 0 0 rgba(255, 107, 157, 0)
            }
        }

        .lnum {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.3rem;
            font-weight: 600;
            color: var(--rose);
            min-width: 76px;
            transition: .3s
        }

        .llbl {
            font-size: .72rem;
            color: var(--text2);
            letter-spacing: .1em;
            text-transform: uppercase;
            line-height: 1.75
        }

        .lsep {
            width: 1px;
            height: 34px;
            background: rgba(44, 24, 16, .1);
            margin: 0 4px
        }

        /* ─── SECTIONS ─────────────────────── */
        .sec {
            position: relative;
            z-index: 10;
            padding: 96px 56px
        }

        .stag {
            font-size: .68rem;
            letter-spacing: .26em;
            text-transform: uppercase;
            color: var(--rose);
            margin-bottom: 13px;
            font-weight: 500
        }

        .stit {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(2rem, 5vw, 3.8rem);
            font-weight: 300;
            line-height: 1.2;
            color: var(--text);
            margin-bottom: 16px
        }

        .stit em {
            font-style: italic;
            background: linear-gradient(135deg, var(--rose), var(--peach));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text
        }

        /* WHY */
        .wSec {
            background: rgba(255, 251, 245, .9);
            backdrop-filter: blur(24px)
        }

        .wGrid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(265px, 1fr));
            gap: 18px;
            margin-top: 50px
        }

        .wCard {
            background: rgba(255, 255, 255, .75);
            border: 1.5px solid rgba(255, 255, 255, .95);
            border-radius: 24px;
            padding: 34px 30px;
            transition: .3s;
            backdrop-filter: blur(12px);
            box-shadow: 0 2px 16px rgba(0, 0, 0, .04)
        }

        .wCard:hover {
            transform: translateY(-6px);
            box-shadow: 0 18px 50px rgba(0, 0, 0, .09)
        }

        .wCard:nth-child(1) {
            border-top: 3px solid var(--rose)
        }

        .wCard:nth-child(2) {
            border-top: 3px solid var(--lemon)
        }

        .wCard:nth-child(3) {
            border-top: 3px solid var(--lavender)
        }

        .wCard:nth-child(4) {
            border-top: 3px solid var(--mint)
        }

        .wCard:nth-child(5) {
            border-top: 3px solid var(--sky)
        }

        .wCard:nth-child(6) {
            border-top: 3px solid var(--peach)
        }

        .wIco {
            font-size: 2.3rem;
            margin-bottom: 16px;
            display: block
        }

        .wCard h3 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.45rem;
            font-weight: 400;
            margin-bottom: 9px;
            color: var(--text)
        }

        .wCard p {
            font-size: .87rem;
            line-height: 1.82;
            color: var(--text2)
        }

        /* STATS — vivid gradient band */
        .sSec {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(175px, 1fr));
            position: relative;
            z-index: 10;
            background: linear-gradient(135deg, var(--rose) 0%, var(--peach) 40%, var(--lemon) 80%, var(--mint) 100%);
            padding: 0 56px
        }

        .sItem {
            text-align: center;
            padding: 54px 28px;
            border-right: 1px solid rgba(255, 255, 255, .3)
        }

        .sItem:last-child {
            border-right: none
        }

        .sNum {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(2.8rem, 5vw, 4.5rem);
            font-weight: 300;
            color: #fff;
            display: block;
            line-height: 1;
            text-shadow: 0 2px 20px rgba(0, 0, 0, .12)
        }

        .sLbl {
            font-size: .72rem;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .82);
            margin-top: 7px;
            display: block
        }

        /* EVENT CARDS */
        .eSec {
            background: rgba(255, 251, 245, .9);
            backdrop-filter: blur(24px)
        }

        .eGrid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(225px, 1fr));
            gap: 17px;
            margin-top: 50px
        }

        .eCard {
            position: relative;
            border-radius: 22px;
            overflow: hidden;
            aspect-ratio: 3/4;
            cursor: none;
            box-shadow: 0 8px 32px rgba(0, 0, 0, .1);
            transition: .4s
        }

        .eCard:hover {
            transform: scale(1.04);
            box-shadow: 0 22px 55px rgba(0, 0, 0, .18)
        }

        .eBg {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 5rem
        }

        .eCard:nth-child(1) .eBg {
            background: linear-gradient(160deg, #ffd6e8, #ff9ec3)
        }

        .eCard:nth-child(2) .eBg {
            background: linear-gradient(160deg, #fff3b0, #ffd166)
        }

        .eCard:nth-child(3) .eBg {
            background: linear-gradient(160deg, #e0d4ff, #b8a0f8)
        }

        .eCard:nth-child(4) .eBg {
            background: linear-gradient(160deg, #c8efff, #7dd3fc)
        }

        .eOv {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(44, 24, 16, .72) 0%, rgba(44, 24, 16, .05) 55%, transparent 100%);
            padding: 22px;
            display: flex;
            flex-direction: column;
            justify-content: flex-end
        }

        .eOv h3 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.6rem;
            font-weight: 400;
            color: #fff;
            margin-bottom: 5px
        }

        .eOv p {
            font-size: .78rem;
            color: rgba(255, 255, 255, .78);
            line-height: 1.6
        }

        .eTag {
            display: inline-block;
            background: rgba(255, 255, 255, .22);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, .4);
            color: #fff;
            font-size: .6rem;
            letter-spacing: .15em;
            text-transform: uppercase;
            padding: 4px 10px;
            border-radius: 20px;
            margin-bottom: 8px;
            align-self: flex-start
        }

        /* TESTIMONIALS */
        .tSec {
            background: linear-gradient(135deg, rgba(184, 160, 248, .12), rgba(255, 230, 109, .12));
            backdrop-filter: blur(20px);
            text-align: center
        }

        .tTrack {
            overflow: hidden;
            margin-top: 50px
        }

        .tInner {
            display: flex;
            gap: 18px;
            width: max-content;
            animation: scroll 34s linear infinite
        }

        .tInner:hover {
            animation-play-state: paused
        }

        @keyframes scroll {
            0% {
                transform: translateX(0)
            }

            100% {
                transform: translateX(-50%)
            }
        }

        .tCard {
            background: rgba(255, 255, 255, .72);
            border: 1.5px solid rgba(255, 255, 255, .95);
            border-radius: 22px;
            padding: 28px 24px;
            width: 315px;
            flex-shrink: 0;
            text-align: left;
            backdrop-filter: blur(12px);
            box-shadow: 0 4px 18px rgba(0, 0, 0, .05)
        }

        .tStar {
            color: var(--lemon);
            font-size: .95rem;
            margin-bottom: 11px;
            letter-spacing: 2px;
            text-shadow: 0 1px 4px rgba(0, 0, 0, .1)
        }

        .tText {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.08rem;
            font-style: italic;
            line-height: 1.76;
            color: var(--text);
            margin-bottom: 17px
        }

        .tAuth {
            display: flex;
            align-items: center;
            gap: 11px
        }

        .tAv {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            background: rgba(255, 107, 157, .1);
            border: 2px solid rgba(255, 107, 157, .25)
        }

        .tName {
            font-size: .83rem;
            font-weight: 500;
            color: var(--text)
        }

        .tOcc {
            font-size: .7rem;
            color: var(--text2)
        }

        /* CTA */
        .cSec {
            text-align: center;
            padding: 110px 24px;
            background: rgba(255, 251, 245, .92);
            backdrop-filter: blur(24px);
            position: relative;
            z-index: 10
        }

        .emo {
            font-size: 2.1rem;
            margin-bottom: 34px;
            letter-spacing: .22em;
            animation: float 4s ease-in-out infinite alternate;
            display: block
        }

        @keyframes float {
            0% {
                transform: translateY(0)
            }

            100% {
                transform: translateY(-10px)
            }
        }

        .cSub {
            font-size: 1rem;
            color: var(--text2);
            margin: 13px auto 42px;
            max-width: 450px;
            line-height: 1.88
        }

        .cFine {
            margin-top: 24px;
            font-size: .73rem;
            color: var(--text2);
            letter-spacing: .1em
        }

        /* FOOTER */
        footer {
            position: relative;
            z-index: 10;
            border-top: 1px solid rgba(44, 24, 16, .08);
            padding: 34px 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: .78rem;
            color: var(--text2);
            background: rgba(255, 251, 245, .96)
        }

        .fLogo {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.15rem;
            color: var(--rose);
            font-weight: 600
        }

        /* UTILS */
        @keyframes fu {
            from {
                opacity: 0;
                transform: translateY(26px)
            }

            to {
                opacity: 1;
                transform: none
            }
        }

        .rv {
            opacity: 0;
            transform: translateY(34px);
            transition: opacity .8s ease, transform .8s ease
        }

        .rv.vis {
            opacity: 1;
            transform: none
        }

        .conf {
            position: fixed;
            pointer-events: none;
            z-index: 200;
            font-size: 1.4rem;
            opacity: 0;
            animation: floatC 5s ease forwards
        }

        @keyframes floatC {
            0% {
                opacity: 0;
                transform: translateY(0) rotate(0)
            }

            10% {
                opacity: .9
            }

            90% {
                opacity: .2
            }

            100% {
                opacity: 0;
                transform: translateY(-85vh) rotate(380deg)
            }
        }

        @media(max-width:768px) {
            nav {
                padding: 18px 18px
            }

            nav ul {
                display: none
            }

            .sec {
                padding: 72px 20px
            }

            .sSec {
                padding: 0 20px;
                grid-template-columns: 1fr 1fr
            }

            .sItem {
                border-right: none;
                border-bottom: 1px solid rgba(255, 255, 255, .3);
                padding: 36px 14px
            }

            footer {
                flex-direction: column;
                gap: 10px;
                text-align: center;
                padding: 26px 18px
            }
        }
    </style>
</head>

<body>

    <div id="cur"></div>
    <div id="curR"></div>

    <!-- SLIDESHOW -->
    <div id="ss"></div>
    <div id="ssOv"></div>
    <div id="dots"></div>

    <!-- SPARKLES -->
    <canvas id="cv"></canvas>

    <!-- NAV -->
    <nav>
        <a href="#" class="logo">Celebrate<b>Hub</b></a>
        <ul>
            <li><a href="#why">Why Us</a></li>
            <li><a href="#events">Events</a></li>
            <li><a href="#stories">Stories</a></li>
            <li><a href="login.php" class="nCta">Book Now</a></li>
        </ul>
    </nav>

    <!-- HERO -->
    <section class="hero">
        <div class="eyebrow">✦ The Premier Celebration Platform ✦</div>
        <h1>Every moment<br>deserves to be<em>legendary</em></h1>
        <div class="pills">
            <div class="pill"><em>💍</em> Weddings</div>
            <div class="pill"><em>🎂</em> Birthdays</div>
            <div class="pill"><em>🥂</em> Anniversaries</div>
            <div class="pill"><em>🎓</em> Graduations</div>
            <div class="pill"><em>✨</em> Celebrations</div>
        </div>
        <p class="sub">From intimate birthday brunches to grand wedding galas — we craft the moments that make hearts overflow and memories that last a lifetime.</p>
        <div class="btns">
            <a href="register.php" class="bPri">Start Planning Free</a>
            <a href="login.php" class="bGhost">Sign In →</a>
        </div>
        <div class="live">
            <div class="ldot"></div>
            <div class="lnum" id="lnum">247</div>
            <div class="llbl">celebrations<br>booked this month</div>
            <div class="lsep"></div>
            <div style="font-size:1.7rem">🎉</div>
            <div class="llbl">and counting<br>every minute</div>
        </div>
    </section>

    <!-- WHY -->
    <section id="why" class="sec wSec" style="max-width:1200px;margin:0 auto;border-radius:0">
        <p class="stag rv">✦ Why CelebrateHub</p>
        <h2 class="stit rv">Crafted for <em>joy</em>,<br>built for perfection</h2>
        <p class="rv" style="color:var(--text2);max-width:490px;line-height:1.88;font-size:.95rem">We don't just manage bookings — we orchestrate experiences that leave guests speechless.</p>
        <div class="wGrid">
            <div class="wCard rv"><span class="wIco">🏛️</span>
                <h3>Stunning Venues</h3>
                <p>Curated spaces from intimate garden pavilions to grand ballrooms — each waiting to become your backdrop.</p>
            </div>
            <div class="wCard rv"><span class="wIco">⚡</span>
                <h3>Zero Conflicts</h3>
                <p>Our intelligent scheduling engine prevents double-bookings in real time, so your perfect day is always only yours.</p>
            </div>
            <div class="wCard rv"><span class="wIco">🎯</span>
                <h3>Instant Registration</h3>
                <p>Guests register in seconds, organizers see live headcounts, everyone notified automatically — zero chaos, all joy.</p>
            </div>
            <div class="wCard rv"><span class="wIco">💎</span>
                <h3>Capacity Mastery</h3>
                <p>Every event has a perfect number. We help you find it — so the energy stays electric all night long.</p>
            </div>
            <div class="wCard rv"><span class="wIco">🎪</span>
                <h3>Resource Magic</h3>
                <p>PA systems, flower arrangements, dance floors — all managed in one place, nothing ever forgotten.</p>
            </div>
            <div class="wCard rv"><span class="wIco">🌟</span>
                <h3>Live Dashboard</h3>
                <p>Real-time analytics on registrations, capacity alerts, and conflict detection — every detail, always under control.</p>
            </div>
        </div>
    </section>

    <!-- STATS -->
    <div class="sSec rv">
        <div class="sItem"><span class="sNum" data-target="1200">0</span><span class="sLbl">Events Hosted</span></div>
        <div class="sItem"><span class="sNum" data-target="98">0</span><span class="sLbl">% Satisfaction</span></div>
        <div class="sItem"><span class="sNum" data-target="8">0</span><span class="sLbl">Stunning Venues</span></div>
        <div class="sItem"><span class="sNum" data-target="300">0</span><span class="sLbl">Max Guests / Event</span></div>
    </div>

    <!-- EVENT TYPES -->
    <section id="events" class="sec eSec" style="max-width:1200px;margin:0 auto">
        <p class="stag rv">✦ What We Celebrate</p>
        <h2 class="stit rv">Every kind of <em>wonderful</em></h2>
        <div class="eGrid">
            <div class="eCard rv">
                <div class="eBg">💍</div>
                <div class="eOv"><span class="eTag">Most Popular</span>
                    <h3>Weddings</h3>
                    <p>The grandest love story — flowers, laughter, and tears of joy. Up to 300 of your closest people.</p>
                </div>
            </div>
            <div class="eCard rv" style="transition-delay:.1s">
                <div class="eBg">🎂</div>
                <div class="eOv"><span class="eTag">Fan Favourite</span>
                    <h3>Birthdays</h3>
                    <p>From sweet sixteens to golden seventies — every year of life deserves a spectacular celebration.</p>
                </div>
            </div>
            <div class="eCard rv" style="transition-delay:.2s">
                <div class="eBg">🥂</div>
                <div class="eOv"><span class="eTag">Milestones</span>
                    <h3>Anniversaries</h3>
                    <p>Twenty-five years, fifty years — the champagne gets better with time. So does the party.</p>
                </div>
            </div>
            <div class="eCard rv" style="transition-delay:.3s">
                <div class="eBg">🎓</div>
                <div class="eOv"><span class="eTag">Achievements</span>
                    <h3>Graduations</h3>
                    <p>Years of hard work, one spectacular night. Walk in knowing you planned something extraordinary.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- TESTIMONIALS -->
    <section id="stories" class="sec tSec">
        <p class="stag">✦ Stories of Joy</p>
        <h2 class="stit">They <em>celebrated</em>.<br>Here's what they said.</h2>
        <div class="tTrack">
            <div class="tInner">
                <div class="tCard">
                    <div class="tStar">★★★★★</div>
                    <p class="tText">"Our wedding was flawless. Every guest registered online, the venue was perfectly set, and we didn't worry about a single thing all day."</p>
                    <div class="tAuth">
                        <div class="tAv">👰</div>
                        <div>
                            <div class="tName">Sarah & Michael</div>
                            <div class="tOcc">Wedding · Grand Ballroom</div>
                        </div>
                    </div>
                </div>
                <div class="tCard">
                    <div class="tStar">★★★★★</div>
                    <p class="tText">"My daughter's sweet sixteen was the talk of the school. The booking system was so easy — I planned everything from my phone!"</p>
                    <div class="tAuth">
                        <div class="tAv">🎂</div>
                        <div>
                            <div class="tName">Karen Thomas</div>
                            <div class="tOcc">Birthday · Garden Pavilion</div>
                        </div>
                    </div>
                </div>
                <div class="tCard">
                    <div class="tStar">★★★★★</div>
                    <p class="tText">"The conflict detection saved us from a double-booking disaster. The team at CelebrateHub really thought of everything."</p>
                    <div class="tAuth">
                        <div class="tAv">🏆</div>
                        <div>
                            <div class="tName">David Brown</div>
                            <div class="tOcc">Corporate Gala · Riverside Hall</div>
                        </div>
                    </div>
                </div>
                <div class="tCard">
                    <div class="tStar">★★★★★</div>
                    <p class="tText">"We booked our 25th anniversary in minutes. The live capacity tracking meant we knew exactly who was coming. Magical evening!"</p>
                    <div class="tAuth">
                        <div class="tAv">💑</div>
                        <div>
                            <div class="tName">The Garcias</div>
                            <div class="tOcc">Anniversary · Rooftop Terrace</div>
                        </div>
                    </div>
                </div>
                <div class="tCard">
                    <div class="tStar">★★★★★</div>
                    <p class="tText">"Graduation party for 200 guests, zero headaches. The platform handled everything — we just showed up and celebrated."</p>
                    <div class="tAuth">
                        <div class="tAv">🎓</div>
                        <div>
                            <div class="tName">Emma Davis</div>
                            <div class="tOcc">Graduation · Outdoor Meadow</div>
                        </div>
                    </div>
                </div>
                <!-- duplicate for seamless loop -->
                <div class="tCard">
                    <div class="tStar">★★★★★</div>
                    <p class="tText">"Our wedding was flawless. Every guest registered online, the venue was perfectly set, and we didn't worry about a single thing all day."</p>
                    <div class="tAuth">
                        <div class="tAv">👰</div>
                        <div>
                            <div class="tName">Sarah & Michael</div>
                            <div class="tOcc">Wedding · Grand Ballroom</div>
                        </div>
                    </div>
                </div>
                <div class="tCard">
                    <div class="tStar">★★★★★</div>
                    <p class="tText">"My daughter's sweet sixteen was the talk of the school. The booking system was so easy — I planned everything from my phone!"</p>
                    <div class="tAuth">
                        <div class="tAv">🎂</div>
                        <div>
                            <div class="tName">Karen Thomas</div>
                            <div class="tOcc">Birthday · Garden Pavilion</div>
                        </div>
                    </div>
                </div>
                <div class="tCard">
                    <div class="tStar">★★★★★</div>
                    <p class="tText">"The conflict detection saved us from a double-booking disaster. The team at CelebrateHub really thought of everything."</p>
                    <div class="tAuth">
                        <div class="tAv">🏆</div>
                        <div>
                            <div class="tName">David Brown</div>
                            <div class="tOcc">Corporate Gala · Riverside Hall</div>
                        </div>
                    </div>
                </div>
                <div class="tCard">
                    <div class="tStar">★★★★★</div>
                    <p class="tText">"We booked our 25th anniversary in minutes. The live capacity tracking meant we knew exactly who was coming. Magical evening!"</p>
                    <div class="tAuth">
                        <div class="tAv">💑</div>
                        <div>
                            <div class="tName">The Garcias</div>
                            <div class="tOcc">Anniversary · Rooftop Terrace</div>
                        </div>
                    </div>
                </div>
                <div class="tCard">
                    <div class="tStar">★★★★★</div>
                    <p class="tText">"Graduation party for 200 guests, zero headaches. The platform handled everything — we just showed up and celebrated."</p>
                    <div class="tAuth">
                        <div class="tAv">🎓</div>
                        <div>
                            <div class="tName">Emma Davis</div>
                            <div class="tOcc">Graduation · Outdoor Meadow</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cSec">
        <p class="stag">✦ Your Moment Awaits</p>
        <span class="emo">🎉 💍 🎂 🥂 ✨</span>
        <h2 class="stit">Ready to make<br><em>memories</em>?</h2>
        <p class="cSub">Join thousands of families who chose CelebrateHub for the moments that matter most. Your perfect event is one click away.</p>
        <div class="btns">
            <a href="register.php" class="bPri">Create Free Account</a>
            <a href="login.php" class="bGhost">I Already Have an Account</a>
        </div>
        <p class="cFine">NO CREDIT CARD REQUIRED &nbsp;·&nbsp; FREE TO JOIN &nbsp;·&nbsp; BOOK IN MINUTES</p>
    </section>

    <!-- FOOTER -->
    <footer>
        <div><span class="fLogo">CelebrateHub</span><span style="margin-left:12px;font-size:.78rem">CINS 5305 · Event Scheduling System</span></div>
        <div>Built with ♥ for every celebration</div>
    </footer>

    <script>
        // ─── SLIDESHOW ───────────────────────────────────────────────
        const SLIDES = [
            'https://images.unsplash.com/photo-1519741497674-611481863552?w=1800&q=88',
            'https://images.unsplash.com/photo-1530103862676-de8c9debad1d?w=1800&q=88',
            'https://images.unsplash.com/photo-1464366400600-7168b8af9bc3?w=1800&q=88',
            'https://images.unsplash.com/photo-1527529482837-4698179dc6ce?w=1800&q=88',
            'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=1800&q=88',
            'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=1800&q=88',
            'https://images.unsplash.com/photo-1470217957101-da7150b9b681?w=1800&q=88',
            'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?w=1800&q=88',
        ];

        const ssEl = document.getElementById('ss');
        const dotsEl = document.getElementById('dots');
        let cur = 0,
            timer;

        // Build slide elements + dots
        SLIDES.forEach((url, i) => {
            const s = document.createElement('div');
            s.className = 'sl' + (i === 0 ? ' on' : '');
            s.style.backgroundImage = `url('${url}')`;
            ssEl.appendChild(s);

            const d = document.createElement('div');
            d.className = 'dot' + (i === 0 ? ' on' : '');
            d.onclick = () => goTo(i);
            dotsEl.appendChild(d);
        });

        function goTo(n) {
            const sls = ssEl.querySelectorAll('.sl');
            const dots = dotsEl.querySelectorAll('.dot');

            // Fade out current
            sls[cur].classList.remove('on');
            sls[cur].classList.add('out');
            dots[cur].classList.remove('on');
            const old = cur;
            setTimeout(() => sls[old].classList.remove('out'), 1800);

            cur = (n + SLIDES.length) % SLIDES.length;

            // Restart Ken Burns by force-reflow
            const next = sls[cur];
            next.classList.remove('on');
            void next.offsetWidth; // reflow
            next.classList.add('on');
            dots[cur].classList.add('on');

            clearInterval(timer);
            timer = setInterval(() => goTo(cur + 1), 5800);
        }

        timer = setInterval(() => goTo(cur + 1), 5800);

        // ─── CURSOR ──────────────────────────────────────────────────
        const cur_ = document.getElementById('cur');
        const curR = document.getElementById('curR');
        let mx = 0,
            my = 0,
            rx = 0,
            ry = 0;
        document.addEventListener('mousemove', e => {
            mx = e.clientX;
            my = e.clientY;
            cur_.style.left = mx + 'px';
            cur_.style.top = my + 'px';
        });
        (function ra() {
            rx += (mx - rx) * .11;
            ry += (my - ry) * .11;
            curR.style.left = rx + 'px';
            curR.style.top = ry + 'px';
            requestAnimationFrame(ra);
        })();
        document.querySelectorAll('a,button,.wCard,.eCard,.pill,.dot').forEach(el => {
            el.addEventListener('mouseenter', () => {
                cur_.style.transform = 'translate(-50%,-50%) scale(2.2)';
                curR.style.width = '56px';
                curR.style.height = '56px';
                curR.style.borderColor = 'rgba(255,107,157,.7)';
            });
            el.addEventListener('mouseleave', () => {
                cur_.style.transform = 'translate(-50%,-50%) scale(1)';
                curR.style.width = '38px';
                curR.style.height = '38px';
                curR.style.borderColor = 'rgba(255,107,157,.45)';
            });
        });

        // ─── SPARKLES ─────────────────────────────────────────────────
        const cv = document.getElementById('cv');
        const ctx = cv.getContext('2d');
        let W, H;
        const resize = () => {
            W = cv.width = innerWidth;
            H = cv.height = innerHeight;
        };
        resize();
        addEventListener('resize', resize);
        const cols = ['rgba(255,107,157,', 'rgba(255,154,124,', 'rgba(184,160,248,', 'rgba(100,200,255,', 'rgba(255,230,109,', 'rgba(92,230,200,'];
        const pts = Array.from({
            length: 55
        }, () => ({
            x: Math.random() * 1920,
            y: Math.random() * 1080,
            r: Math.random() * 1.8 + .4,
            a: Math.random(),
            da: (Math.random() - .5) * .012,
            c: cols[Math.floor(Math.random() * cols.length)]
        }));
        (function draw() {
            ctx.clearRect(0, 0, W, H);
            pts.forEach(p => {
                p.a += p.da;
                if (p.a < 0 || p.a > 1) p.da *= -1;
                ctx.beginPath();
                ctx.arc(p.x % W, p.y % H, p.r, 0, Math.PI * 2);
                ctx.fillStyle = p.c + p.a + ')';
                ctx.fill();
            });
            requestAnimationFrame(draw);
        })();

        // ─── LIVE COUNTER ────────────────────────────────────────────
        let lc = 247;
        const lEl = document.getElementById('lnum');
        setInterval(() => {
            if (Math.random() < .3) {
                lc++;
                lEl.textContent = lc;
                lEl.style.transform = 'scale(1.18)';
                lEl.style.color = 'var(--coral)';
                setTimeout(() => {
                    lEl.style.transform = 'scale(1)';
                    lEl.style.color = 'var(--rose)';
                }, 420);
            }
        }, 2800);

        // ─── SCROLL REVEAL ───────────────────────────────────────────
        const ro = new IntersectionObserver(entries => {
            entries.forEach((e, i) => {
                if (e.isIntersecting) setTimeout(() => e.target.classList.add('vis'), i * 65);
            });
        }, {
            threshold: .1
        });
        document.querySelectorAll('.rv').forEach(el => ro.observe(el));

        // ─── STAT COUNTERS ───────────────────────────────────────────
        const so = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                const el = entry.target,
                    tgt = +el.dataset.target;
                const step = tgt / 100;
                let n = 0;
                const t = () => {
                    n = Math.min(n + step, tgt);
                    el.textContent = Math.floor(n).toLocaleString() + (el.dataset.target === '98' ? '%' : '+');
                    if (n < tgt) requestAnimationFrame(t);
                };
                t();
                so.unobserve(el);
            });
        }, {
            threshold: .5
        });
        document.querySelectorAll('[data-target]').forEach(el => so.observe(el));

        // ─── CLICK CONFETTI ──────────────────────────────────────────
        const emos = ['🎉', '✨', '💍', '🎂', '🥂', '🌸', '💫', '🎊', '❤️', '🌷', '🎈', '🌟', '🫧', '🍾'];
        document.addEventListener('click', e => {
            for (let i = 0; i < 8; i++) {
                setTimeout(() => {
                    const d = document.createElement('div');
                    d.className = 'conf';
                    d.textContent = emos[Math.floor(Math.random() * emos.length)];
                    d.style.cssText = `left:${e.clientX+(Math.random()-.5)*110}px;top:${e.clientY}px;animation-duration:${3.5+Math.random()*3}s;font-size:${.85+Math.random()*1.1}rem`;
                    document.body.appendChild(d);
                    setTimeout(() => d.remove(), 6500);
                }, i * 65);
            }
        });
    </script>
</body>

</html>