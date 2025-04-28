<?php include 'menu.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SkillSwap</title>
</head>
<body>

  <header>
    <h1>SKILLSWAP</h1>
  </header>

  <img src="https://www.easygenerator.com/wp-content/uploads/2020/10/knowledge-sharing-workplace.png" alt="Knowledge Sharing" class="hero-image">

  <div class="background-shapes">
    <div class="shape"></div>
    <div class="shape"></div>
    <div class="shape"></div>
    <div class="shape"></div>
    <div class="shape"></div>
    <div class="shape"></div>
  </div>

  <div class="container">

    <div class="section">
      <h2>What is SkillSwap?</h2>
      <p>SkillSwap is a platform where people share and barter their skills. Instead of money, users exchange what they are good at for something new they want to learn.</p>
    </div>

    <div class="section">
      <h2>The Problem We Solve</h2>
      <p>Many people want to learn new skills but lack the money for formal education. At the same time, they have talents they can offer. SkillSwap bridges that gap by connecting people through skill exchange.</p>
    </div>

    <div class="section">
      <h2>Features</h2>
      <ul>
        <li><strong>Skill Exchange:</strong> Offer your skills and learn new ones from others in your community.</li>
        <li><strong>Community Building:</strong> Foster meaningful connections through shared learning experiences.</li>
        <li><strong>Free of Charge:</strong> No money involved, just a passion for learning and teaching.</li>
      </ul>
    </div>

    <div class="section">
      <h2>How It Works</h2>
      <ul>
        <li>Post the skill you want to learn and what you can offer in return</li>
        <li>Browse the community for people looking for your expertise</li>
        <li>Connect, agree on an exchange, and start growing your talents</li>
        <li>Learn new things while helping others develop their strengths</li>
      </ul>
    </div>

  </div>

</body>
</html>

<style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

  body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #FFD700, #FFF8DC);
    color: #000;
    overflow-x: hidden;
  }

  header {
    background-color: #FFD700;
    padding: 40px 20px;
    text-align: center;
    position: relative;
  }

  header h1 {
    margin: 0;
    color: #000000;
    font-size: 3rem;
  }

  .hero-image {
    width: 100%;
    max-height: 400px;
    object-fit: cover;
  }

  .container {
    padding: 60px 20px;
    max-width: 1200px;
    margin: auto;
    position: relative;
    z-index: 2;
  }

  .section {
    margin-bottom: 60px;
  }

  h2 {
    color: #000;
    font-size: 2.5rem;
    margin-bottom: 20px;
    position: relative;
  }

  h2::after {
    content: '';
    width: 60px;
    height: 4px;
    background: #000;
    position: absolute;
    bottom: -10px;
    left: 0;
  }

  p, li {
    color: #333;
    font-size: 1.2rem;
    margin-top: 10px;
  }

  ul {
    list-style: disc;  
    padding-left: 20px;  
  }

  .cta-button {
    display: inline-block;
    padding: 15px 35px;
    background-color: #000;
    color: #FFD700;
    font-weight: bold;
    font-size: 1.2rem;
    text-decoration: none;
    border-radius: 50px;
    transition: all 0.3s ease;
  }

  .cta-button:hover {
    background-color: #333;
    transform: scale(1.05);
    box-shadow: 0px 0px 20px #000;
  }

  .section-center {
    text-align: center;
  }

  .background-shapes {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    z-index: 0;
    pointer-events: none;
  }

  .shape {
    position: absolute;
    background: rgba(0, 0, 0, 0.2);
    border-radius: 50%;
    animation: float 6s ease-in-out infinite;
  }

  .background-shapes div:nth-child(1) { width: 80px; height: 80px; top: 10%; left: 5%; animation-delay: 0s; }
  .background-shapes div:nth-child(2) { width: 120px; height: 120px; top: 30%; left: 80%; animation-delay: 1s; }
  .background-shapes div:nth-child(3) { width: 60px; height: 60px; top: 70%; left: 20%; animation-delay: 2s; }
  .background-shapes div:nth-child(4) { width: 100px; height: 100px; top: 50%; left: 40%; animation-delay: 3s; }
  .background-shapes div:nth-child(5) { width: 90px; height: 90px; top: 20%; left: 60%; animation-delay: 4s; }
  .background-shapes div:nth-child(6) { width: 70px; height: 70px; top: 80%; left: 75%; animation-delay: 5s; }

  @keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-20px); }
  }
</style>
