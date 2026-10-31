<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0a192f">
    <title>اکتوشاپ | فروشگاه اینترنتی خودت را ساده راه‌اندازی کن</title>
    <style>
        @font-face{font-family:IRANYekan;src:url('/fonts/iranyekannum/woff/iranyekanwebregularfanum.woff') format('woff');font-weight:400;font-display:swap}
        @font-face{font-family:IRANYekan;src:url('/fonts/iranyekannum/woff/iranyekanwebboldfanum.woff') format('woff');font-weight:700;font-display:swap}
        :root{--navy:#0a192f;--navy2:#102746;--cyan:#39d6c4;--ink:#15243a;--muted:#64748b;--line:#e6edf2;--paper:#f7fafb}
        *{box-sizing:border-box}html{scroll-behavior:smooth}body{margin:0;font-family:IRANYekan,Tahoma,sans-serif;color:var(--ink);background:#fff;line-height:1.9}a{text-decoration:none;color:inherit}.container{width:min(1160px,calc(100% - 40px));margin:auto}
        .topbar{height:82px;display:flex;align-items:center;border-bottom:1px solid rgba(255,255,255,.09);position:absolute;z-index:3;top:0;left:0;right:0;color:#fff}.nav{display:flex;align-items:center;justify-content:space-between}.brand{display:flex;align-items:center;gap:11px;font-size:21px;font-weight:700}.brand-mark{width:40px;height:40px;border-radius:13px;background:var(--cyan);display:grid;place-items:center;color:var(--navy);font-size:23px}.brand small{display:block;font-size:10px;line-height:1;color:#9db4c8;font-weight:400;letter-spacing:1px}.navlinks{display:flex;align-items:center;gap:30px;font-size:13px;color:#c4d2df}.navlinks a:hover{color:var(--cyan)}.navlinks .nav-sample{color:var(--cyan)}.nav-cta,.btn{display:inline-flex;align-items:center;justify-content:center;border-radius:10px;padding:11px 20px;font:700 13px IRANYekan;transition:.2s}.nav-cta{background:var(--cyan);color:var(--navy)}.btn-primary{background:var(--cyan);color:var(--navy);box-shadow:0 8px 25px #39d6c42b}.btn-primary:hover,.nav-cta:hover{transform:translateY(-2px);filter:brightness(1.04)}.btn-ghost{border:1px solid #ffffff40;color:#fff}
        .hero{background:radial-gradient(ellipse at 80% 25%,#17466a 0,transparent 38%),linear-gradient(125deg,#08172c,#102d4b);color:#fff;padding:158px 0 95px;position:relative;overflow:hidden}.hero:after{content:"";position:absolute;width:520px;height:520px;border:1px solid #ffffff0c;border-radius:50%;left:-210px;bottom:-340px;box-shadow:0 0 0 55px #ffffff08,0 0 0 110px #ffffff05}.hero-grid{display:grid;grid-template-columns:1.08fr .92fr;gap:65px;align-items:center;position:relative;z-index:1}.eyebrow{display:inline-flex;align-items:center;gap:8px;color:var(--cyan);font-size:12px;font-weight:700;margin-bottom:18px}.eyebrow:before{content:"";width:22px;height:2px;background:var(--cyan)}h1{font-size:clamp(32px,4.2vw,52px);line-height:1.55;margin:0 0 18px;letter-spacing:-1.2px}.hero-copy{color:#c3d1df;font-size:15px;max-width:570px;margin:0 0 29px;line-height:2.2}.hero-actions{display:flex;gap:12px;flex-wrap:wrap}.hero-note{font-size:11px;color:#9fb2c4;margin-top:18px}.mockup-wrap{position:relative;min-height:355px;display:grid;place-items:center}.glow{position:absolute;width:310px;height:310px;background:#36d9c51c;filter:blur(65px);border-radius:50%}.shop-window{width:min(100%,440px);background:#fff;border-radius:18px;overflow:hidden;box-shadow:0 28px 75px #0006;transform:rotate(-2deg);position:relative}.window-head{height:36px;background:#edf3f5;display:flex;align-items:center;gap:5px;padding:0 14px}.window-head i{width:7px;height:7px;background:#c4d1d8;border-radius:50%}.window-body{padding:22px;color:#14243a}.mini-store-head{display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #edf0f2;padding-bottom:13px;font-size:11px;font-weight:700}.mini-logo{background:#e7faf7;color:#138f82;padding:6px 9px;border-radius:7px}.mini-banner{margin-top:15px;background:linear-gradient(110deg,#dff7f2,#f2f9f7);border-radius:12px;padding:18px;display:flex;justify-content:space-between;align-items:center;min-height:125px}.mini-banner strong{font-size:17px;display:block;line-height:1.8}.mini-banner span{font-size:9px;color:#617a7c}.mini-art{font-size:55px;color:#169e91}.mini-products{display:grid;grid-template-columns:repeat(3,1fr);gap:9px;margin-top:14px}.mini-product{border:1px solid #edf0f2;border-radius:9px;padding:8px;text-align:center}.mini-product b{display:block;background:#f2f5f7;border-radius:6px;height:48px;margin-bottom:7px;font-size:25px;color:#8aa3aa}.mini-product span{font-size:8px;color:#667}.float-chip{position:absolute;background:#fff;color:#1a3448;padding:10px 14px;border-radius:11px;box-shadow:0 12px 35px #0003;font-size:11px;font-weight:700}.chip-one{top:22px;right:-5px}.chip-two{bottom:25px;left:-4px}.chip-dot{display:inline-block;width:8px;height:8px;border-radius:50%;background:#39c7a9;margin-left:6px}
        .trust-strip{background:#fff;border-bottom:1px solid var(--line)}.trust-items{display:grid;grid-template-columns:repeat(3,1fr);padding:22px 0;gap:18px}.trust-item{display:flex;align-items:center;justify-content:center;gap:10px;color:#526579;font-size:12px}.trust-item strong{color:#14243a}.trust-icon{width:34px;height:34px;border-radius:10px;background:#e9f9f6;color:#159d8e;display:grid;place-items:center;font-size:17px}
        section{padding:88px 0}.section-head{text-align:center;max-width:650px;margin:0 auto 42px}.section-kicker{color:#159d8e;font-size:11px;font-weight:700}.section-head h2{font-size:29px;line-height:1.7;margin:8px 0;color:var(--navy)}.section-head p{font-size:13px;color:var(--muted);margin:0}.feature-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:17px}.feature{border:1px solid var(--line);border-radius:15px;padding:25px 23px;background:#fff;transition:.2s}.feature:hover{border-color:#a7e8df;transform:translateY(-3px);box-shadow:0 15px 35px #0a192f0a}.feature-icon{width:45px;height:45px;border-radius:13px;background:#e9f9f6;color:#159d8e;display:grid;place-items:center;font-size:21px;margin-bottom:17px}.feature h3{font-size:15px;margin:0 0 8px}.feature p{font-size:12px;color:var(--muted);line-height:2;margin:0}
        .how{background:var(--paper)}.steps{display:grid;grid-template-columns:repeat(4,1fr);gap:15px;counter-reset:step}.step{background:#fff;border:1px solid var(--line);border-radius:14px;padding:22px;position:relative}.step-num{width:34px;height:34px;border-radius:10px;background:var(--navy);color:var(--cyan);display:grid;place-items:center;font-weight:700;font-size:13px;margin-bottom:16px}.step h3{font-size:14px;margin:0 0 7px}.step p{font-size:11px;color:var(--muted);margin:0;line-height:2}
        .split{display:grid;grid-template-columns:1fr 1fr;gap:65px;align-items:center}.split h2{font-size:29px;line-height:1.7;margin:8px 0 14px;color:var(--navy)}.split p{font-size:13px;color:var(--muted);line-height:2.2}.check-list{list-style:none;padding:0;margin:22px 0}.check-list li{font-size:12px;margin:11px 0;display:flex;gap:10px;align-items:flex-start}.check{color:#159d8e;font-weight:700}.info-panel{background:linear-gradient(145deg,#0a192f,#153b59);border-radius:20px;padding:30px;color:#fff;position:relative;overflow:hidden}.info-panel:after{content:"";position:absolute;width:220px;height:220px;border:1px solid #ffffff18;border-radius:50%;left:-100px;bottom:-120px;box-shadow:0 0 0 30px #ffffff08,0 0 0 60px #ffffff05}.info-panel h3{font-size:17px;margin:0 0 18px}.info-row{display:flex;gap:12px;padding:13px 0;border-bottom:1px solid #ffffff18;position:relative;z-index:1}.info-row:last-child{border:0}.info-row b{color:var(--cyan);font-size:17px}.info-row span{font-size:12px;color:#d2deea}
        .request{background:#f4f8fa}.request-grid{display:grid;grid-template-columns:.8fr 1.2fr;gap:55px;align-items:start}.request-copy h2{font-size:29px;color:var(--navy);line-height:1.7;margin:8px 0 12px}.request-copy p{font-size:13px;color:var(--muted);line-height:2.2}.request-hint{margin-top:25px;padding:15px;border-right:3px solid var(--cyan);background:#e9f8f5;border-radius:8px;font-size:11px;color:#42615f}.request-form{background:#fff;border:1px solid var(--line);border-radius:17px;padding:28px;box-shadow:0 15px 40px #0a192f08}.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:15px}.field{display:flex;flex-direction:column;gap:7px}.field.full{grid-column:1/-1}.field label{font-size:11px;font-weight:700;color:#34465a}.field input,.field select,.field textarea{width:100%;border:1px solid #dfe7ec;border-radius:9px;padding:12px 13px;font:12px IRANYekan;color:var(--ink);background:#fff;outline:none;transition:.2s}.field input:focus,.field select:focus,.field textarea:focus{border-color:#48cabb;box-shadow:0 0 0 3px #39d6c41c}.field textarea{min-height:92px;resize:vertical}.form-submit{margin-top:18px;width:100%;border:0;cursor:pointer}.form-foot{font-size:10px;color:#82909c;text-align:center;margin:11px 0 0}
        .pricing-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px;max-width:850px;margin:0 auto}.plan-card{position:relative;background:#0e263a;border:1px solid #20384b;border-radius:18px;padding:30px}.plan-card.economic{border-color:#41faf1;box-shadow:0 15px 45px #0002}.plan-tag{display:inline-block;padding:5px 11px;border-radius:30px;background:#12394a;color:#41faf1;font-size:10px;font-weight:700}.plan-card h3{font-size:19px;margin:14px 0 5px;color:#f1fbfc}.plan-desc{font-size:11px;color:#a6bac8;margin:0 0 18px}.plan-price{font-size:28px;font-weight:700;color:#41faf1;line-height:1.6}.plan-price small{font-size:11px;font-weight:400;color:#a6bac8}.plan-list{list-style:none;padding:0;margin:20px 0 0}.plan-list li{font-size:12px;color:#d5e3e9;padding:9px 0;border-bottom:1px solid #ffffff12}.plan-list li:last-child{border:0}.plan-action{display:flex;margin-top:22px}.plan-action .btn{width:100%}.pricing-note{text-align:center;color:#9db2c1;font-size:11px;margin:22px 0 0}
        .sample-showcase{padding:58px 0;background:#0a192f;border-top:1px solid #20384b;border-bottom:1px solid #20384b}.sample-inner{display:flex;align-items:center;justify-content:space-between;gap:30px}.sample-copy{max-width:690px}.sample-copy .section-kicker{color:var(--cyan)}.sample-copy h2{font-size:23px;color:#f1fbfc;margin:7px 0}.sample-copy p{font-size:12px;color:#a6bac8;margin:0}.sample-link{white-space:nowrap;flex-shrink:0}
        .faq-list{max-width:800px;margin:auto;display:grid;gap:10px}.faq{border:1px solid var(--line);border-radius:11px;padding:16px 19px}.faq summary{font-size:13px;font-weight:700;cursor:pointer}.faq p{font-size:12px;color:var(--muted);margin:10px 0 0;line-height:2}
        footer{background:var(--navy);color:#c3d1df;padding:34px 0}.footer-inner{display:flex;justify-content:space-between;align-items:center;gap:20px}.footer-inner p{font-size:11px;margin:0;color:#91a6b9}.footer-brand{color:#fff;font-weight:700;font-size:16px}
        @media(max-width:900px){.hero-grid{gap:25px}.hero{padding-top:135px}.mockup-wrap{min-height:300px}.feature-grid{grid-template-columns:repeat(2,1fr)}.steps{grid-template-columns:repeat(2,1fr)}.split{gap:30px}.request-grid{gap:28px}}
        @media(max-width:640px){.pricing-grid{grid-template-columns:1fr}.navlinks{display:flex;gap:12px;font-size:10px}.navlinks a:nth-child(2),.navlinks a:nth-child(3){display:none}.sample-inner{align-items:flex-start;flex-direction:column;gap:18px}.sample-copy h2{font-size:20px}.container{width:min(100% - 30px,520px)}.topbar{height:68px}.navlinks{display:none}.brand{font-size:18px}.brand-mark{width:36px;height:36px}.nav-cta{padding:9px 13px;font-size:11px}.hero{padding:112px 0 55px}.hero-grid{grid-template-columns:1fr;gap:30px}.hero h1{font-size:31px;line-height:1.65}.hero-copy{font-size:13px}.hero-actions .btn{flex:1;padding:12px 10px;font-size:11px}.mockup-wrap{min-height:275px;padding:0 8px}.shop-window{transform:none}.chip-one{right:-2px;top:8px}.chip-two{left:-2px;bottom:8px}.trust-items{grid-template-columns:1fr;gap:12px;padding:17px 0}.trust-item{justify-content:flex-start}.trust-item:not(:first-child){display:none}section{padding:60px 0}.section-head{margin-bottom:28px}.section-head h2,.split h2,.request-copy h2{font-size:24px}.feature-grid{grid-template-columns:1fr;gap:11px}.feature{padding:20px}.steps{grid-template-columns:1fr 1fr;gap:10px}.step{padding:16px}.step p{font-size:10px}.split,.request-grid{grid-template-columns:1fr;gap:25px}.info-panel{padding:23px}.request-form{padding:19px}.form-grid{grid-template-columns:1fr;gap:12px}.field.full{grid-column:auto}.footer-inner{flex-direction:column;align-items:flex-start}.footer-inner p{line-height:2}}

        /* Octoopus brand palette */
        :root{--navy:#0a192f;--navy2:#0e263a;--cyan:#41faf1;--ink:#eaf4f7;--muted:#a6bac8;--line:#20384b;--paper:#0e263a}
        body{background:var(--navy);color:var(--ink)}
        .topbar{background:rgba(10,25,47,.88);backdrop-filter:blur(12px);border-bottom-color:#ffffff12}
        .brand-mark{background:var(--cyan);color:var(--navy)}
        .nav-cta,.btn-primary{background:var(--cyan);color:var(--navy);box-shadow:0 8px 25px #41faf12b}
        .btn-ghost{border-color:#41faf14d;color:#eaf4f7}
        .hero{background:radial-gradient(ellipse at 80% 25%,#0e263a 0,transparent 48%),linear-gradient(125deg,#0a192f,#0e263a)}
        .glow{background:#0e263a;box-shadow:0 0 85px 35px #0e263a}
        .trust-strip{background:#0e263a;border-bottom-color:#ffffff12}
        .trust-item{color:#a6bac8}.trust-item strong{color:#f1fbfc}
        .trust-icon,.feature-icon{background:#12394a;color:var(--cyan)}
        .section-head h2,.split h2,.request-copy h2{color:#f1fbfc}
        .section-kicker{color:var(--cyan)}
        .feature{background:#0e263a;border-color:#20384b}
        .feature:hover{border-color:#41faf180;box-shadow:0 15px 35px #0002}
        .feature p,.section-head p,.split p,.step p,.request-copy p,.faq p{color:#a6bac8}
        .how{background:#0e263a}
        .step{background:#0a192f;border-color:#20384b}
        .step-num{background:#12394a;color:var(--cyan)}
        .info-panel{background:linear-gradient(145deg,#0a192f,#0e263a);border:1px solid #20384b}
        .info-row{border-bottom-color:#ffffff18}.info-row b{color:var(--cyan)}
        .request{background:#0e263a}
        .request-hint{background:#12394a;border-right-color:var(--cyan);color:#c3d8df}
        .request-form{background:#0a192f;border-color:#20384b;box-shadow:0 15px 40px #0002}
        .field label{color:#d8e7ed}
        .field input,.field select,.field textarea{background:#0e263a;border-color:#294357;color:#f1fbfc}
        .field input::placeholder,.field textarea::placeholder{color:#8299a9}
        .field input:focus,.field select:focus,.field textarea:focus{border-color:var(--cyan);box-shadow:0 0 0 3px #41faf11c}
        .form-foot{color:#8299a9}
        .faq{border-color:#20384b;background:#0e263a}
        .faq summary{color:#eaf4f7}
        footer{background:#071426;border-top:1px solid #20384b}
        .float-chip{color:#163044}
        @media(max-width:640px){.trust-strip{background:#0e263a}}

    </style>
</head>
<body>
<header class="topbar"><div class="container nav">
    <a class="brand" href="#"><span class="brand-mark">⌁</span><span>اکتوشاپ<small>فروشگاهت، ساده‌تر از همیشه</small></span></a>
    <nav class="navlinks"><a href="#pricing">پلن‌ها</a><a href="#features">امکانات</a><a href="#how">نحوه کار</a><a class="nav-sample" href="https://chicmod.ir/" target="_blank" rel="noopener noreferrer">نمونه فروشگاه ↗</a></nav>
    <a class="nav-cta" href="#request">درخواست راه‌اندازی</a>
</div></header>
<main>
<section class="hero"><div class="container hero-grid">
    <div>
        <span class="eyebrow">راه‌اندازی فروشگاه اینترنتی، بدون دردسرهای فنی</span>
        <h1>برای فروش آنلاین،<br>لازم نیست از صفر سایت بسازی.</h1>
        <p class="hero-copy">اکتوشاپ راه ساده‌تری برای شروع فروش اینترنتی است. یک فروشگاه آماده و اختصاصی داشته باش، محصولاتت را ثبت کن و سفارش‌هایت را از یک پنل ساده مدیریت کن؛ بدون نیاز به خرید هاست، طراحی سایت یا درگیری با تنظیمات پیچیده.</p>
        <div class="hero-actions"><a class="btn btn-primary" href="#request">می‌خواهم فروشگاهم را راه بیندازم</a><a class="btn btn-ghost" href="#how">ببین چطور کار می‌کند</a></div>
        <div class="hero-note">درخواستت را ثبت کن؛ برای بررسی و هماهنگی با تو تماس می‌گیریم.</div>
    </div>
    <div class="mockup-wrap"><div class="glow"></div>
        <div class="shop-window"><div class="window-head"><i></i><i></i><i></i></div><div class="window-body">
            <div class="mini-store-head"><span>فروشگاه نمونه</span><span class="mini-logo">فروشگاه من</span></div>
            <div class="mini-banner"><div><strong>محصولاتت را<br>آنلاین بفروش</strong><span>ساده، سریع و مستقل</span></div><div class="mini-art">▧</div></div>
            <div class="mini-products"><div class="mini-product"><b>◈</b><span>محصول اول</span></div><div class="mini-product"><b>◇</b><span>محصول دوم</span></div><div class="mini-product"><b>▱</b><span>محصول سوم</span></div></div>
        </div></div>
        <div class="float-chip chip-one"><span class="chip-dot"></span>پنل مدیریت ساده</div><div class="float-chip chip-two">✓ سفارش‌ها یک‌جا جمع می‌شوند</div>
    </div>
</div></section>
<div class="trust-strip"><div class="container trust-items">
    <div class="trust-item"><span class="trust-icon">⌂</span><span><strong>فروشگاه اختصاصی</strong><br>با آدرس مستقل</span></div>
    <div class="trust-item"><span class="trust-icon">▤</span><span><strong>مدیریت آسان</strong><br>محصول و سفارش در یک پنل</span></div>
    <div class="trust-item"><span class="trust-icon">↗</span><span><strong>شروع ساده</strong><br>بدون نیاز به دانش فنی</span></div>
</div></div>
<section id="features"><div class="container">
    <div class="section-head"><span class="section-kicker">چرا اکتوشاپ؟</span><h2>به‌جای ساختن سایت، روی فروش تمرکز کن</h2><p>زیرساخت فروشگاه آماده است؛ تو روی چیزی تمرکز می‌کنی که برای کسب‌وکارت مهم‌تر است: محصول، مشتری و سفارش.</p></div>
    <div class="feature-grid">
        <article class="feature"><div class="feature-icon">⌘</div><h3>فروشگاه آماده با آدرس اختصاصی</h3><p>فروشگاهت را با دامنه و هویت خودت در اختیار مشتری‌ها قرار بده؛ بدون اینکه درگیر طراحی و راه‌اندازی فنی شوی.</p></article>
        <article class="feature"><div class="feature-icon">▦</div><h3>مدیریت محصولات و محتوا</h3><p>محصولات، دسته‌بندی‌ها و مطالب فروشگاهت را از پنل فروشنده مدیریت کن.</p></article>
        <article class="feature"><div class="feature-icon">▣</div><h3>مدیریت سفارش‌ها</h3><p>سفارش‌های مشتریان را ببین، وضعیت پرداخت را بررسی کن و مراحل آماده‌سازی و ارسال را پیگیری کن.</p></article>
        <article class="feature"><div class="feature-icon">⇄</div><h3>پرداخت متناسب با شروع کار</h3><p>امکان پرداخت کارت‌به‌کارت با ثبت رسید و تأیید فروشنده؛ مناسب شروع فروش بدون پیچیدگی درگاه.</p></article>
        <article class="feature"><div class="feature-icon">⌕</div><h3>پیگیری سفارش برای مشتری</h3><p>مشتری می‌تواند از لینک پیگیری سفارش، وضعیت خریدش را دنبال کند.</p></article>
        <article class="feature"><div class="feature-icon">▧</div><h3>ارتباط و اطلاع‌رسانی</h3><p>با ابزارهای اطلاع‌رسانی فروشگاه، از سفارش‌های جدید باخبر شو و وضعیت آن‌ها را به مشتری اعلام کن.</p></article>
    </div>
</div></section>
<section class="sample-showcase"><div class="container sample-inner">
    <div class="sample-copy"><span class="section-kicker">یک نمونه واقعی از اکتوشاپ</span><h2>فروشگاه نمونه را از نزدیک ببین.</h2><p>برای اینکه با نتیجه کار آشنا شوی، می‌توانی نمونه فروشگاهی را که با اکتوشاپ راه‌اندازی کرده‌ایم بررسی کنی.</p></div>
    <a class="btn btn-primary sample-link" href="https://chicmod.ir/" target="_blank" rel="noopener noreferrer">مشاهده فروشگاه نمونه ↗</a>
</div></section>
<section id="pricing"><div class="container">
    <div class="section-head"><span class="section-kicker">پلن‌های اکتوشاپ</span><h2>با پلن رایگان شروع کن؛ هر وقت خواستی ارتقا بده.</h2><p>برای شروع فروش آنلاین لازم نیست از همان ابتدا هزینه‌ای پرداخت کنی.</p></div>
    <div class="pricing-grid">
        <article class="plan-card">
            <span class="plan-tag">برای شروع</span>
            <h3>پلن رایگان</h3>
            <p class="plan-desc">مناسب شروع و امتحان کردن فروش آنلاین</p>
            <div class="plan-price">رایگان</div>
            <ul class="plan-list">
                <li>ثبت حداکثر ۷ محصول</li>
                <li>ثبت حداکثر ۷ مقاله</li>
                <li>پنل مدیریت فروشگاه</li>
                <li>مدیریت سفارش‌ها</li>
            </ul>
            <div class="plan-action"><a class="btn btn-ghost" href="#request">درخواست پلن رایگان</a></div>
        </article>
        <article class="plan-card economic">
            <span class="plan-tag">برای فروش بیشتر</span>
            <h3>پلن اقتصادی</h3>
            <p class="plan-desc">برای فروشگاه‌هایی که محصولات بیشتری دارند</p>
            <div class="plan-price">۱٬۳۵۰٬۰۰۰ <small>تومان / سالانه</small></div>
            <ul class="plan-list">
                <li>امکان ثبت تا ۵۰ محصول</li>
                <li>امکانات مدیریت فروشگاه و سفارش‌ها</li>
                <li>بدون نیاز به خرید هاست و راه‌اندازی فنی</li>
            </ul>
            <div class="plan-action"><a class="btn btn-primary" href="#request">درخواست پلن اقتصادی</a></div>
        </article>
    </div>
    <p class="pricing-note">برای دریافت جزئیات و شروع کار، درخواستت را ثبت کن تا با تو هماهنگ کنیم.</p>
</div></section>
<section class="how" id="how"><div class="container">
    <div class="section-head"><span class="section-kicker">شروع کار در چند قدم</span><h2>از درخواست تا اولین سفارش</h2><p>قرار نیست با ده‌ها تنظیمات و اصطلاح فنی روبه‌رو شوی. مسیر شروع کوتاه و روشن است.</p></div>
    <div class="steps">
        <article class="step"><div class="step-num">۱</div><h3>درخواستت را ثبت کن</h3><p>اطلاعات اولیه کسب‌وکارت را در فرم همین صفحه وارد کن.</p></article>
        <article class="step"><div class="step-num">۲</div><h3>هماهنگی و بررسی</h3><p>نیازهایت را بررسی می‌کنیم و درباره جزئیات راه‌اندازی با تو هماهنگ می‌شویم.</p></article>
        <article class="step"><div class="step-num">۳</div><h3>دریافت فروشگاه</h3><p>فروشگاهت آماده می‌شود و دسترسی پنل مدیریت را دریافت می‌کنی.</p></article>
        <article class="step"><div class="step-num">۴</div><h3>محصولاتت را منتشر کن</h3><p>محصولات را ثبت کن، لینک فروشگاهت را به اشتراک بگذار و سفارش بگیر.</p></article>
    </div>
</div></section>
<section><div class="container split">
    <div><span class="section-kicker">برای شروع، نه برای پیچیده‌تر کردن کار</span><h2>اگر فقط می‌خواهی آنلاین بفروشی، از همین‌جا شروع کن.</h2><p>اکتوشاپ برای کسب‌وکارهایی طراحی شده که می‌خواهند یک فروشگاه اینترنتی ساده و قابل مدیریت داشته باشند؛ بدون اینکه از همان روز اول هزینه و زمان زیادی صرف زیرساخت و طراحی کنند.</p>
        <ul class="check-list"><li><span class="check">✓</span> نیازی نیست خودت هاست و سایت را راه‌اندازی کنی.</li><li><span class="check">✓</span> مدیریت فروشگاه از یک پنل مشخص انجام می‌شود.</li><li><span class="check">✓</span> می‌توانی محصولات و مقاله‌های فروشگاهت را مدیریت کنی.</li><li><span class="check">✓</span> مشتری مسیر ثبت سفارش و پیگیری مشخصی دارد.</li></ul>
    </div>
    <div class="info-panel"><h3>چه چیزهایی در اختیار فروشگاهت قرار می‌گیرد؟</h3>
        <div class="info-row"><b>01</b><span>صفحه اصلی و صفحات محصولات فروشگاه</span></div><div class="info-row"><b>02</b><span>پنل مدیریت محصولات، سفارش‌ها و مقالات</span></div><div class="info-row"><b>03</b><span>تنظیمات فروشگاه و هزینه ارسال</span></div><div class="info-row"><b>04</b><span>روش پرداخت کارت‌به‌کارت و پیگیری سفارش</span></div><div class="info-row"><b>05</b><span>امکان اطلاع‌رسانی سفارش‌ها</span></div>
    </div>
</div></section>
<section class="request" id="request"><div class="container request-grid">
    <div class="request-copy"><span class="section-kicker">قدم اول را بردار</span><h2>برای راه‌اندازی فروشگاهت درخواست بده.</h2><p>چند اطلاعات کوتاه از کسب‌وکارت برای ما بفرست. بعد از بررسی، برای توضیح شرایط و هماهنگی مراحل بعدی با تو تماس می‌گیریم.</p><div class="request-hint">ثبت این فرم به معنی خرید یا پرداخت نیست؛ فقط درخواستت را برای بررسی و هماهنگی دریافت می‌کنیم.</div></div>
    <form class="request-form" id="platform-request" method="POST" action="{{ route('platform.request') }}">
        @csrf
        @if(session('platform_request_sent'))
            <div role="status" style="padding:12px 14px;margin-bottom:16px;border:1px solid #41faf166;border-radius:9px;background:#12394a;color:#bafaf4;font-size:12px">درخواستت با موفقیت ارسال شد. به‌زودی برای هماهنگی با تو تماس می‌گیریم.</div>
        @endif
        @if($errors->any())
            <div role="alert" style="padding:12px 14px;margin-bottom:16px;border:1px solid #b45309;border-radius:9px;background:#422f20;color:#ffdfb5;font-size:12px">لطفاً اطلاعات واردشده را بررسی کن و دوباره بفرست.</div>
        @endif
        <div class="form-grid">
            <div class="field"><label for="full_name">نام و نام خانوادگی *</label><input id="full_name" name="full_name" value="{{ old('full_name') }}" placeholder="مثلاً مسعود مومنی" maxlength="120" required></div>
            <div class="field"><label for="phone">شماره تماس *</label><input id="phone" name="phone" value="{{ old('phone') }}" inputmode="tel" placeholder="09xxxxxxxxx" maxlength="20" required></div>
            <div class="field"><label for="business">نام کسب‌وکار یا فروشگاه</label><input id="business" name="business" value="{{ old('business') }}" placeholder="نام فروشگاهت" maxlength="150"></div>
            <div class="field"><label for="category">زمینه فعالیت</label><select id="category" name="category"><option value="">انتخاب کن</option><option>پوشاک و اکسسوری</option><option>کالای دیجیتال</option><option>خانه و آشپزخانه</option><option>مواد غذایی</option><option>محصولات دست‌ساز</option><option>سایر</option></select></div>
            <div class="field full"><label for="description">کمی درباره چیزی که می‌فروشی یا نیازت بنویس</label><textarea id="description" name="description" maxlength="2000" placeholder="مثلاً چند محصول دارم و می‌خواهم فروش آنلاین را شروع کنم...">{{ old('description') }}</textarea></div>
        </div>
        <button class="btn btn-primary form-submit" type="submit">ثبت درخواست راه‌اندازی</button><p class="form-foot">اطلاعاتت فقط برای بررسی درخواست و تماس درباره راه‌اندازی استفاده می‌شود.</p>
         
    </form>
</div></section>
<section id="faq"><div class="container"><div class="section-head"><span class="section-kicker">سؤالات متداول</span><h2>قبل از درخواست، این‌ها را بدان</h2></div>
    <div class="faq-list">
        <details class="faq"><summary>برای راه‌اندازی فروشگاه باید طراحی سایت بلد باشم؟</summary><p>خیر. فروشگاه و پنل مدیریت آماده است. بعد از راه‌اندازی می‌توانی اطلاعات و محصولاتت را از پنل وارد کنی.</p></details>
        <details class="faq"><summary>باید هاست یا دامنه تهیه کنم؟</summary><p>هدف سرویس این است که درگیر تهیه و مدیریت زیرساخت فنی نشوی. جزئیات آدرس فروشگاه هنگام هماهنگی راه‌اندازی مشخص می‌شود.</p></details>
        <details class="faq"><summary>مشتری‌ها چطور هزینه سفارش را پرداخت می‌کنند؟</summary><p>در نسخه فعلی، امکان پرداخت کارت‌به‌کارت و ثبت تصویر رسید برای بررسی و تأیید فروشنده در نظر گرفته شده است.</p></details>
        <details class="faq"><summary>چطور سفارش‌ها را مدیریت می‌کنم؟</summary><p>از پنل فروشنده می‌توانی سفارش‌ها را ببینی، پرداخت را بررسی کنی و اطلاعات ارسال یا کد رهگیری را ثبت کنی.</p></details>
        <details class="faq"><summary>پلن رایگان چه محدودیتی دارد؟</summary><p>در پلن رایگان می‌توانی حداکثر ۷ محصول و ۷ مقاله ثبت کنی. اگر به ظرفیت بیشتری نیاز داشتی، پلن اقتصادی تا ۵۰ محصول در دسترس است.</p></details>
        <details class="faq"><summary>هزینه پلن اقتصادی چقدر است؟</summary><p>هزینه پلن اقتصادی سالانه ۱٬۳۵۰٬۰۰۰ تومان است.</p></details>
    </div>
</div></section>
</main>
<footer><div class="container footer-inner"><div><div class="footer-brand">اکتوشاپ</div><p>راه ساده‌تر برای شروع فروش اینترنتی</p></div><p>© تمامی حقوق برای اکتوشاپ محفوظ است.</p></div></footer>
 
</body></html>