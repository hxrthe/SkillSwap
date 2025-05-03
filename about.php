<?php include 'menu.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ABOUT</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: #FFEA61;
      color: #000;
      padding: 20px;
    }

    h1 {
      text-align: center;
      color: #000;
    }

    .container {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 40px;
      margin-top: 40px;
    }

    .card {
      position: relative;
      width: 90%;
      max-width: 700px;
      background-color: #111;
      border: 2px solid #FFD700;
      border-radius: 10px;
      overflow: hidden;
      display: flex;
      margin-top: 20px; 
    }

    .card.right {
      flex-direction: row-reverse;
      margin-left: 50px;
    }

    .container .card:nth-child(1) {
      margin-top: 50px;
      transform: translateX(-380px); 
    }

    .container .card:nth-child(2) {
      margin-top: -340px; 
      transform: translateX(350px); 
    }

    .container .card:nth-child(3) {
      margin-top: 10px; 
      transform: translateX(380px); 
    }

    .container .card:nth-child(4) {
      margin-top: -340px; 
      transform: translateX(-400px); 
    }

    .card-image-container {
      width: 50%;
      height: 300px;
      overflow: hidden;
      cursor: pointer;
    }

    .card img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    .info {
      width: 50%;
      height: 300px;
      background-color: rgba(0, 0, 0, 0.9);
      color: #FFD700;
      padding: 20px;
      box-sizing: border-box;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow: hidden;
      text-align: center;
    }

    .placeholder {
      font-size: 1.8em;
      font-weight: bold;
      transition: opacity 0.4s ease;
      position: absolute;
      z-index: 1;
    }

    .info-text {
      position: absolute;
      opacity: 0;
      z-index: 2;
      transform: translateX(-100%);
      transition: transform 0.5s ease, opacity 0.5s ease;
    }

    .card.right .info-text {
      transform: translateX(100%);
    }

    .card:hover .placeholder {
      opacity: 0;
    }

    .card:hover .info-text {
      transform: translateX(0);
      opacity: 1;
    }

    .info-text ul {
      list-style-type: disc;
      margin-left: 20px;
    }
  </style>
</head>
<body>

  <div class="container">
    <!-- Card 1 - SKILLSWAP -->
    <div class="card">
      <div class="card-image-container">
        <img src="https://scontent.fmnl34-1.fna.fbcdn.net/v/t1.15752-9/482954870_1345965966623948_5736240018260320208_n.png?stp=dst-png_p480x480&_nc_cat=102&ccb=1-7&_nc_sid=0024fc&_nc_eui2=AeGcVN6KdfmUjYMfK9SWadfDsaPUWSiNab-xo9RZKI1pv8Gl_Rd2KNU-8QEDdVil_a-y3YSELzQzuElpiBqHf70U&_nc_ohc=zasVqMshDogQ7kNvwH1GP2T&_nc_oc=Adney4L-IXWwyD-sWLKS0pNjoa1GCjcTCs-AkQUsrHx-gO0hVCaNDVEbY6CeTf3rT2T-aLbqtdJdZHYmWQJesJus&_nc_ad=z-m&_nc_cid=5917&_nc_zt=23&_nc_ht=scontent.fmnl34-1.fna&oh=03_Q7cD2AHUBbG2shl32Rs1Atr-WFC3GVpO0NkWuxnCc1syjsS5mg&oe=683D224F" alt="SkillSwap">
      </div>
      <div class="info">
        <div class="placeholder">SKILLSWAP</div>
        <div class="info-text">
          <p><strong>SkillSwap</strong> is a local, community-based platform designed to empower individuals by exchanging knowledge and skills. It operates like a modern barter system for expertise, where people can teach and learn from each other, all without monetary transactions.</p>
        </div>
      </div>
    </div>

    <!-- Card 2 - FEATURES -->
    <div class="card right">
      <div class="card-image-container">
        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRRtQaYtaQ_zo32rb4uQpaIbFMV8XgBAl56QA&s" alt="Features">
      </div>
      <div class="info">
        <div class="placeholder">FEATURES</div>
        <div class="info-text">
          <ul>
            <li><strong>Skill Exchange:</strong> Offer your skills and learn new ones from others in your community.</li>
            <li><strong>Community Building:</strong> Foster meaningful connections through shared learning experiences.</li>
            <li><strong>Free of Charge:</strong> No money involved, just a passion for learning and teaching.</li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Card 3 - HOW IT WORKS -->
    <div class="card">
      <div class="card-image-container">
        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTAwTfNQvrUBLqS082BWSr2vUQrcOLzPYgjfg&s" alt="How It Works">
      </div>
      <div class="info">
        <div class="placeholder">HOW IT WORKS</div>
        <div class="info-text">
          <ol>
            <li><strong>Sign Up:</strong> Create an account on the platform.</li>
            <li><strong>List Your Skills:</strong> Share what you can teach and specify what you're interested in learning.</li>
            <li><strong>Connect:</strong> Find individuals who match your interests and start swapping skills.</li>
            <li><strong>Collaborate:</strong> Schedule sessions and engage in productive skill exchanges.</li>
          </ol>
        </div>
      </div>
    </div>

    <!-- Card 4 - WHY SKILLSWAP? -->
    <div class="card right">
      <div class="card-image-container">
        <img src="https://cdn.elearningindustry.com/wp-content/uploads/2019/10/7-Benefits-That-Highlight-The-Importance-Of-Soft-Skills-In-The-Workplace.png" alt="Why SkillSwap?">
      </div>
      <div class="info">
        <div class="placeholder">WHY SKILLSWAP?</div>
        <div class="info-text">
          <ul>
            <li><strong>Accessibility:</strong> Learning opportunities for everyone, regardless of financial status.</li>
            <li><strong>Empowerment:</strong> Share your expertise and enrich others' lives.</li>
            <li><strong>Sustainability:</strong> Promote a culture of generosity and mutual growth.</li>
          </ul>
        </div>
      </div>
    </div>

  </div>

</body>
</html>
