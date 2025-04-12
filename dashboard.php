<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header("Location: Interests.php");
    exit();
}
?>

<?php if (isset($_GET['register'])): ?>
  <?php include 'RegistrationForm.php'; ?>
<?php endif; ?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Kdam+Thmor+Pro&display=swap" rel="stylesheet">

</head>
<body>
  <div class="logo"></div>
  <div class="skillswap-text">SkillSwap</div>

  <!-- Navigation -->
  <a href="#" class="nav-home">HOME</a>
  <div class="nav-auth">
    <a href="login.php">LOGIN</a> / 
    <a href="Dashboard.php?register=1">REGISTER</a>
  </div>
  <a href="#" class="nav-aboutus">ABOUT US</a>

  <!-- User Icon -->
  <a href="Profile.php">
    <i class="fa-solid fa-circle-user user-icon"></i>
  </a>

  <div class="intro-text">
    Have a talent or expertise you’re proud of? Maybe you capture stunning photos, ride waves like a pro, or teach languages with ease. And perhaps there’s something you’ve always wanted to pick up—dancing, surfing, or mastering a new language. SkillSwap empowers you to exchange your skills and learn from others, all without spending a dime.
  </div>

  <div class="left-side-image"></div>
  <div class="motto">
    Let your skills shine through
  </div>
  <div class="skill">SKILL<div>
  <div class="swap">SWAP<div>


</body>

<style>
  html, body {
    margin: 0;
    padding: 0;
    height: 100%;
    overflow: hidden;
  }

  body {
    background-image: url('https://scontent.fmnl34-1.fna.fbcdn.net/v/t1.15752-9/488156556_681630060897772_3018384513271027386_n.png?stp=dst-png_s640x640&_nc_cat=100&ccb=1-7&_nc_sid=0024fc&_nc_eui2=AeFTMwZmsqQRDBdoFcj1AOJRIlWHpFqOA_QiVYekWo4D9L0B03XB0bm-K9UM6CuWv_DSQGkpTCaMdl9JRs-wuYZN&_nc_ohc=nCByisxqrFwQ7kNvwGev9C-&_nc_oc=AdkIXeefdLBGToAw5CC9ppf1D-l67QDflxlaEIJA8Fl-GsPE_OKMGFiK1yNw-Mfoyr-P9mrFnfQBVPJyUSZexlDe&_nc_ad=z-m&_nc_cid=5917&_nc_zt=23&_nc_ht=scontent.fmnl34-1.fna&oh=03_Q7cD2AFlcTeKLEs_w1aou6qE6km1KFRXkrVw4qSEegGOJPboow&oe=681D78D2');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
  }

  .logo {
    position: absolute;
    width: 92px;
    height: 92px;
    left: 50px;
    top: 15px;
    background: url('https://scontent.fmnl34-1.fna.fbcdn.net/v/t1.15752-9/487876368_558030910658035_8113894336002225316_n.png?_nc_cat=107&ccb=1-7&_nc_sid=0024fc&_nc_eui2=AeG8KaWJgOE8nBk7Q0jgqTUOnWTUXwewHQGdZNRfB7AdAeH9XBOVxYxu8oKYypu-nBI-wnNNnWqPyVXGXRh3QfLk&_nc_ohc=bfGFbzc7aAsQ7kNvwFmtkU8&_nc_oc=AdnJn15pwUCMQSy5u4QjOPJKXJh6aSH1KrDVz44gd6hjweqCQWC3Vweh_yLmOtjcna3IzbTFUCtBSR9zqnS_sdkI&_nc_ad=z-m&_nc_cid=5917&_nc_zt=23&_nc_ht=scontent.fmnl34-1.fna&oh=03_Q7cD2AE4hzPr02Qgobz4XAH8jCK_Oq9JVQY97e1U4zyt33doRg&oe=681D4A2B') no-repeat center;
    background-size: contain;
    filter: brightness(0) invert(1);
  }

  .skillswap-text {
    position: absolute;
    width: 110px;
    height: 37px;
    left: 150px;
    top: 40px;
    font-family: 'Kdam Thmor Pro', sans-serif;
    font-weight: 400;
    font-size: 24px;
    line-height: 37px;
    color: #ffffff;
    z-index: 5;
  }

  .nav-home,
  .nav-aboutus {
    position: absolute;
    top: 30px;
    font-family: 'Inter';
    font-weight: bold;
    font-size: 32px;
    color: white;
    text-decoration: none;
    z-index: 2;
  }

  .nav-home { left: 720px; }
  .nav-aboutus { left: 1225px; }

  .nav-auth {
    position: absolute;
    top: 30px;
    left: 880px;
    font-family: 'Inter';
    font-size: 32px;
    color: white;
    font-weight: bold;
    z-index: 2;
  }

  .nav-auth a {
    color: white;
    text-decoration: none;
    margin: 0 5px;
  }

  .user-icon {
    position: absolute;
    top: 20px;
    right: 70px;
    font-size: 50px;
    color: white;
    z-index: 3;
  }

  .intro-text {
    position: absolute;
    width: 700px;
    left: 750px;
    top: 175px;
    font-family: 'Instrument Serif', serif;
    font-size: 40px;
    line-height: 52px;
    text-align: justify;
    letter-spacing: 0.1em;
    color: #FFFFFF;
    text-shadow: 3px 2px 4px #000000;
  }

  .left-side-image {
    position: absolute;
    width: 842px;
    height: 840px;
    left: 20px;
    top: 20px;
    background: url('https://scontent.fmnl34-1.fna.fbcdn.net/v/t1.15752-9/482954870_1345965966623948_5736240018260320208_n.png?stp=dst-png_p480x480&_nc_cat=102&ccb=1-7&_nc_sid=0024fc&_nc_eui2=AeGcVN6KdfmUjYMfK9SWadfDsaPUWSiNab-xo9RZKI1pv8Gl_Rd2KNU-8QEDdVil_a-y3YSELzQzuElpiBqHf70U&_nc_ohc=Nm5yEJOULE0Q7kNvwHwHV5C&_nc_oc=AdmHd9eN2vvRY9MrwxrifIUvLIzsDxZK0a-zQ7heinyU-w7SaUCBEndh-shf84dKKEM&_nc_ad=z-m&_nc_cid=5917&_nc_zt=23&_nc_ht=scontent.fmnl34-1.fna&oh=03_Q7cD2AHoaWsyAbOTryvYBCFKK5NQDKrsVrEkr0eImUAc6Aprxw&oe=6820214F') no-repeat center;
    background-size: contain;
    z-index: 1;
  }

  .motto {
    position: absolute;
    width: 500px;
    height: 37px;
    left: 220px;
    top: 170px;
    font-family: 'Instrument Serif', sans-serif;
    font-weight: 400;
    font-size: 36px;
    color: #ffffff;
    z-index: 5;
  }

  .skill {
    position: absolute;
    width: 500px;
    height: 100px;
    left: 410px;
    top: 280px;
    font-family: 'Kdam Thmor Pro', sans-serif;
    font-weight: 400;
    font-size: 96px;
    color: #ffffff;
    z-index: 5;
  }

  .swap {
    position: absolute;
    width: 500px;
    height: 100px;
    left: -280px;
    top: 180px;
    font-family: 'Kdam Thmor Pro', sans-serif;
    font-weight: 400;
    font-size: 96px;
    color: #ffffff;
    z-index: 5;
  }
  
</style>

</body>
</html>

