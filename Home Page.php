<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>HomePage</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Kdam+Thmor+Pro&display=swap" rel="stylesheet">

  </head>
<body>
  
  <!-- Navigation -->
  <a href="#" class="nav-home">HOME</a>
  <a href="#" class="nav-inbox">INBOX</a>
  <a href="#" class="nav-search">SEARCH</a>
  <a href="#" class="nav-community">COMMUNITY</a>


  <!-- Logo -->
  <div class="logo-image"></div>
  <div class="skillswap-text">SkillSwap</div>

  <!-- User Icon -->
  <a href="Profile.php">
  <i class="fa-solid fa-circle-user user-icon"></i></a>


  <!-- Search Bar -->
  <div class="search-bar">
    <input type="text" placeholder="Search..." />
    <div class="search-icon">
      <i class="fa-solid fa-magnifying-glass"></i>
    </div>
  </div>

  
  <div class="matches-text">Matches</div>
  <a href="#" class="see-all-text">See all matches ></a>

  <div class="background-overlay"></div>

  <!--  Ellipses -->
  <svg class="ellipse top-right" viewBox="0 0 603 485">
    <ellipse cx="301.5" cy="242.5" rx="301.5" ry="242.5" fill="#EEFE07" />
  </svg>

  <svg class="ellipse middle-right" viewBox="0 0 400 324">
    <ellipse cx="200" cy="162" rx="200" ry="162" fill="#EEFE07" />
  </svg>

  <svg class="ellipse bottom-right" viewBox="0 0 521 399">
    <ellipse cx="260.5" cy="199.5" rx="260.5" ry="199.5" fill="#EEFE07" />
  </svg>

  
  <div class="box-1"></div>
  <div class="box-2">
    <!-- Person Icon inside Box 2 -->
    <div class="person">
      <i class="fa-solid fa-circle-user"></i>
    </div>
  </div>

  <!-- Card Content -->
  <div class="name">Name</div>
  <div class="topic">Topic</div>
  <div class="location">Location</div>
  <div class="location-icon">
    <i class="fa-solid fa-location-dot"></i>
  </div>
  <div class="ellipse-5"></div>
  <div class="will-offer-you">WILL OFFER YOU</div>
  <div class="in-exchange-for">IN EXCHANGE FOR</div>
  <div class="box-3"></div>
  <div class="box-4"></div>

  <!-- Body Text Elements -->
  <div class="hi-pal">Hi PAL!</div>
  <div class="skills-message">Let your skills shine through</div>
  <div class="skillswap-title">SKILLSWAP</div>

  <style>
    body {
      margin: 0;
      height: 100vh;
      padding-bottom: 100px;
      background: linear-gradient(to right, yellow, white);
      position: relative;
      overflow: hidden;
      font-family: sans-serif;
    }

    .background-overlay {
      position: absolute;
      width: 746px;
      height: 745px;
      left: 750px;
      top: 130px;
      background: url('https://scontent.fmnl34-1.fna.fbcdn.net/v/t1.15752-9/488835906_8948145041951672_4701581188130643661_n.png?stp=dst-png_p480x480&_nc_cat=101&ccb=1-7&_nc_sid=0024fc&_nc_eui2=AeGHroDfscELDaXTstPdw6sRZSKOpCBQDlVlIo6kIFAOVZL6MKHpXFEAnnb1cOT1l4AnrlI-foUVQnBjnN6nsr2G&_nc_ohc=6rp0ZD9-F58Q7kNvwGyTS4J&_nc_oc=AdlgIErAM9K7ziuJ5G6lIZDca4_yb3YaZJpCSGbsI7iPAFCjq96P9yxFRfbx-pMnlRbZX1wsZF-GNaC1Z3MixeHW&_nc_ad=z-m&_nc_cid=5917&_nc_zt=23&_nc_ht=scontent.fmnl34-1.fna&oh=03_Q7cD2AH-gADlMwTtWOeb3H1UqAOC_zvqLHurA1gTDKoLn967LA&oe=681D5ACB');
      background-size: cover;
      opacity: 0.7;
      z-index: -2;
    }

    .ellipse {
      position: absolute;
      z-index: 1;
    }

    .top-right {
      width: 603px;
      height: 485px;
      top: -190px;
      right: -100px;
    }

    .middle-right {
      width: 400px;
      height: 324px;
      top: 300px;
      right: -170px;
    }

    .bottom-right {
      width: 521px;
      height: 399px;
      bottom: -110px;
      right: -90px;
    }

    .nav-home,
    .nav-inbox,
    .nav-search,
    .nav-community {
      position: absolute;
      top: 30px;
      font-size: 29px;
      color: black;
      font-weight: bold;
      text-decoration: none;
      z-index: 2;
    }

    .nav-home { left: 625px; }
    .nav-inbox { left: 800px; }
    .nav-search { left: 975px; }
    .nav-community { left: 1150px; }

    .user-icon {
      position: absolute;
      top: 20px;
      right: 100px;
      font-size: 50px;
      color: black;
      z-index: 3;
    }

    .search-bar {
      display: flex;
      flex-direction: row;
      align-items: center;
      position: absolute;
      width: 660px;
      min-width: 360px;
      max-width: 720px;
      height: 69px;
      left: 78px;
      top: 135px;
      background: #FFFFFF;
      border-radius: 28px;
    }

    .search-bar input {
      flex: 1;
      padding: 10px;
      border: none;
      border-radius: 20px;
      font-size: 16px;
      outline: none;
    }

    .search-bar .search-icon i {
      color: #000;
      font-size: 24px;
      margin: 0 30px;
    }

    .box-1 {
      position: absolute;
      width: 600.42px;
      height: 400.09px;
      left: 120px;
      top: 330px;
      background: #ffffff;
      box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
      border-radius: 10px;
      transform: matrix(1, -0.06, 0.05, 1, 0, 0);
    }

    .box-2 {
      position: absolute;
      width: 600px;
      height: 400px;
      left: 150px;
      top: 325px;
      background: #ffffff;
      box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
      border-radius: 10px;
    }

    .name {
      position: absolute;
      width: 102px;
      height: 50px;
      left: 420px;
      top: 350px;
      font-weight: 700;
      font-size: 29px;
      color: #000000;
    }

    .topic {
      position: absolute;
      left: 420px;
      top: 390px;
      font-size: 20px;
      color: #000000;
    }

    .location {
      position: absolute;
      left: 420px;
      top: 420px;
      font-weight: 700;
      font-size: 24px;
      color: #000000;
    }

    .location-icon {
      position: absolute;
      width: 63px;
      height: 49px;
      left: 360px;
      top: 410px;
      font-size: 30px;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .will-offer-you {
      position: absolute;
      left: 250px;
      top: 525px;
      font-size: 16px;
      color: #000000;
    }

    .in-exchange-for {
      position: absolute;
      left: 530px;
      top: 525px;
      font-size: 16px;
      color: #000000;
    }

    .box-3 {
      position: absolute;
      width: 160px;
      height: 52px;
      left: 240px;
      top: 560px;
      background: #EEFE07;
      border-radius: 10px;
    }

    .box-4 {
      position: absolute;
      width: 160px;
      height: 52px;
      left: 520px;
      top: 560px;
      background: #FFE135;
      border-radius: 10px;
    }

    .person {
      position: absolute;
      width: 83px;
      height: 94px;
      left: 100px;
      top: 40px;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .person i {
      font-size: 40px;
      font-size: 120px;
      color: #000;
    }

    .skillswap-text {
      position: absolute;
      width: 110px;
      height: 37px;
      left: 150px;
      top: 40px;
      font-family: 'Kdam Thmor Pro', sans-serif;
      font-style: normal;
      font-weight: 400;
      font-size: 24px;
      line-height: 37px;
      color: #000000;
      z-index: 5;
    }

    .matches-text {
      position: absolute;
      width: 614px;
      height: 51px;
      left: 85px;
      top: 240px;
      font-family: 'Inter';
      font-style: normal;
      font-weight: 700;
      font-size: 40px;
      line-height: 48px;
      color: #000000;
    }

    .see-all-text {
      position: absolute;
      width: 614px;
      height: 51px;
      left: 571px;
      top: 255px;
      font-family: 'Inter';
      font-style: normal;
      font-weight: 400;
      font-size: 20px;
      line-height: 24px;
      color: #000000;
      text-decoration: none;
    }

    .hi-pal {
      position: absolute;
      width: 614px;
      height: 45px;
      left: 1210px;
      top: 345px;
      font-family: 'Kdam Thmor Pro', sans-serif;
      font-style: normal;
      font-weight: 400;
      font-size: 64px;
      line-height: 99px;
      color: #000000;
      text-shadow: 0px 4px 4px #FFFFFF;
      z-index: 10;
    }

    .skills-message {
      position: absolute;
      width: 388px;
      height: 45px;
      left: 1000px;
      top: 480px;
      font-family: 'Instrument Serif', serif;
      font-style: normal;
      font-weight: 400;
      font-size: 35px;
      line-height: 52px;
      color: #000000;
      text-shadow: 0px 4px 4px #FFFFFF;
      z-index: 10;
      white-space: nowrap;
    }

    .skillswap-title {
      position: absolute;
      width: 534px;
      height: 148px;
      left: 790px;
      top: 550px;
      font-family: 'Kdam Thmor Pro', sans-serif;
      font-style: normal;
      font-weight: 400;
      font-size: 96px;
      line-height: 148px;
      color: #000000;
      z-index: 10;
    }
    
    .logo-image {
      position: absolute;
      width: 92px;
      height: 92px;
      left: 50px;
      top: 15px;
      color: #000000;
      background: url('https://scontent.fmnl34-1.fna.fbcdn.net/v/t1.15752-9/487876368_558030910658035_8113894336002225316_n.png?_nc_cat=107&ccb=1-7&_nc_sid=0024fc&_nc_eui2=AeG8KaWJgOE8nBk7Q0jgqTUOnWTUXwewHQGdZNRfB7AdAeH9XBOVxYxu8oKYypu-nBI-wnNNnWqPyVXGXRh3QfLk&_nc_ohc=bfGFbzc7aAsQ7kNvwFmtkU8&_nc_oc=AdnJn15pwUCMQSy5u4QjOPJKXJh6aSH1KrDVz44gd6hjweqCQWC3Vweh_yLmOtjcna3IzbTFUCtBSR9zqnS_sdkI&_nc_ad=z-m&_nc_cid=5917&_nc_zt=23&_nc_ht=scontent.fmnl34-1.fna&oh=03_Q7cD2AE4hzPr02Qgobz4XAH8jCK_Oq9JVQY97e1U4zyt33doRg&oe=681D4A2B');
      background-size: cover; /* Ensures the image fills the div */
    }
    
  </style>
</body>
</html>
