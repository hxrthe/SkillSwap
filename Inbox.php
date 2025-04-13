<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Inbox</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Kdam+Thmor+Pro&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">


  </head>
<body>
  
  <!-- Navigation -->
  <a href="Home Page.php" class="nav-home">HOME</a>
  <a href="Inbox.php" class="nav-inbox">INBOX</a>
  <a href="Search.php" class="nav-search">SEARCH</a>
  <a href="community.php" class="nav-community">COMMUNITY</a>

  <div class="background-overlay"></div>

  <!-- Logo -->
  <div class="logo-image"></div>
  <div class="skillswap-text">SkillSwap</div>

  <!-- User Icon -->
  <a href="Profile.php">
  <i class="fa-solid fa-circle-user user-icon"></i></a>

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

  <div class="status-tabs">
  <div class="status-tab" data-tab="request">Request</div>
  <div class="status-tab" data-tab="sent">Sent</div>
  <div class="status-tab" data-tab="ongoing">Ongoing</div>
  <div class="status-tab" data-tab="completed">Completed</div>
  <div class="tab-indicator"></div>
</div>


 <!-- Linear-Indeterminate Progress Indicator -->
  <div class="linear-indeterminate">
  <div class="track-container">
  <div class="track">
  </div>

  <div class="linear-indeterminate" style="top: 140px;">
  <div class="track-container">
  <div class="track">
  <div class="indeterminate-bar"></div>
  </div>


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

    .skillswap-text {
      position: absolute;
      width: 110px;
      height: 37px;
      left: 150px;
      top: 30px;
      font-family: 'Kdam Thmor Pro', sans-serif;
      font-style: normal;
      font-weight: 400;
      font-size: 24px;
      line-height: 37px;
      color: #000000;
      z-index: 5;
    }

    /* Add logo styling */
    .logo-image {
      position: absolute;
      width: 92px;
      height: 92px;
      left: 50px;
      top: 10px;
      color: #000000;
      background: url('https://scontent.fmnl34-1.fna.fbcdn.net/v/t1.15752-9/487876368_558030910658035_8113894336002225316_n.png?_nc_cat=107&ccb=1-7&_nc_sid=0024fc&_nc_eui2=AeG8KaWJgOE8nBk7Q0jgqTUOnWTUXwewHQGdZNRfB7AdAeH9XBOVxYxu8oKYypu-nBI-wnNNnWqPyVXGXRh3QfLk&_nc_ohc=bfGFbzc7aAsQ7kNvwFmtkU8&_nc_oc=AdnJn15pwUCMQSy5u4QjOPJKXJh6aSH1KrDVz44gd6hjweqCQWC3Vweh_yLmOtjcna3IzbTFUCtBSR9zqnS_sdkI&_nc_ad=z-m&_nc_cid=5917&_nc_zt=23&_nc_ht=scontent.fmnl34-1.fna&oh=03_Q7cD2AE4hzPr02Qgobz4XAH8jCK_Oq9JVQY97e1U4zyt33doRg&oe=681D4A2B');
      background-size: cover; /* Ensures the image fills the div */
    }

    .status-tabs {
     position: absolute;
     top: 192px;
     left: 75px;
     display: flex;
     gap: 260px;
     z-index: 5;
    }

    .status-tab {
    font-family: 'Inter', sans-serif;
    font-size: 32px;
    cursor: pointer;
    position: relative;
    padding: 5px 10px;
   }

    .tab-indicator {
    position: absolute;
    height: 4px;
    width: 100px;
    background: black;
    bottom: -10px;
    left: 0;
    transition: all 0.3s ease;
    }


    .linear-indeterminate {
    position: absolute;
    width: 1440px;
    height: 12px;
    left: 0px;
    top: 240px;
    }

    .linear-indeterminate .track-container {
    /* Auto layout */
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: flex-start;
    padding: 4px 0px 4px 2px;

    position: absolute;
    height: 12px;
    left: 0%;
    right: 0%;
    top: calc(50% - 12px / 2);
    z-index: 3;
    }

    .linear-indeterminate .track {
    /* Track shape */
    width: 1600px;
    height: 4px;
    background: #E8DEF8; /* M3/sys/light/secondary-container */
    border-radius: 8px;

    /* Inside auto layout */
    flex: none;
    order: 0;
    align-self: stretch;
    flex-grow: 0;
    }


    .linear-indeterminate {
    position: absolute;
    width: 1440px;
    height: 12px;
    left: 0px;
    top: 240px; /* Default top position */
    }

    .linear-indeterminate.lower {
    top: 280px; /* Lower positioned duplicate */
    }

    .linear-indeterminate .track-container {
    /* Auto layout */
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: flex-start;
    padding: 4px 0px 4px 2px;

    position: absolute;
    height: 12px;
    left: 0%;
    right: 0%;
    top: calc(50% - 12px / 2);

    }

    .linear-indeterminate .track {
    width: 1600px;
    height: 4px;
    background: #E8DEF8; /* M3/sys/light/secondary-container */
    border-radius: 8px;

    flex: none;
    order: 0;
    align-self: stretch;
    flex-grow: 0;
    }
  </style>

<script>
  const tabs = document.querySelectorAll('.status-tab');
  const indicator = document.querySelector('.tab-indicator');

  function moveIndicator(tab) {
    const tabRect = tab.getBoundingClientRect();
    const containerRect = tab.parentElement.getBoundingClientRect();
    const offset = tabRect.left - containerRect.left;

    indicator.style.left = offset + 'px';
    indicator.style.width = tabRect.width + 'px';
  }

  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      moveIndicator(tab);
    });
  });

  // Optional: set default to the first tab
  window.onload = () => {
    moveIndicator(tabs[0]);
  };
</script>

</body>
</html>

