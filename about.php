<?php include 'menu.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ABOUT US</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(to right, #fdfd96, #fff);
      color: #f2f2f2;
    }

    body.hovered-background {
      background-size: cover;
      background-position: center;
      transition: background-image 0.5s ease-in-out;
    }

    h2 {
      font-size: 2.5rem;
      font-family: 'Great Vibes', cursive;
      background: #000000;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 40px;
      text-shadow: 1px 1px 0 #000; 
    }

    .service-card {
      border-radius: 12px;
      padding: 30px;
      border: 2px solid transparent;
      transition: transform 0.3s ease, background-color 0.3s ease, box-shadow 0.3s ease;
      text-align: center;
      cursor: pointer;
      height: 100%;
    }

    .service-card:hover {
      border-color: #ffd54f;
      box-shadow: 0 0 15px rgba(255, 213, 79, 0.5);
      transform: scale(1.05);
    }

    .service-card i {
      font-size: 40px;
      color: #fdd835;
      margin-bottom: 15px;
      transition: color 0.3s;
    }

    .service-card:hover i {
      color: #ffeb3b;
    }

    .service-title {
      font-weight: bold;
      font-size: 1.2rem;
      margin-bottom: 10px;
      color: #fdd835;
    }

    .service-text {
      font-size: 0.95rem;
      color: #d6d6d6;
    }

    /* Skill Exchange card */
    .skill-exchange-card {
      position: relative;
      background-image: url('https://media.istockphoto.com/id/1285788819/vector/experience-and-knowledge-exchange.jpg?s=612x612&w=0&k=20&c=85u-i0mtnp7bPXrY78huwZVA5VXXeyVFN6jjBjfqQcY=');
      background-size: cover;
      background-position: center;
      color: #fff;
      overflow: hidden;
    }

    .skill-exchange-card::before {
      content: "";
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      background-color: rgba(0, 0, 0, 0.7); 
      z-index: 0;
      border-radius: 12px; 
    }

    .skill-exchange-card > * {
      position: relative;
      z-index: 1;
      text-shadow: 1px 1px 3px rgba(0,0,0,0.7);
    }

    /* Free learning opportunities */
    .free-learning-opportunities {
      position: relative;
      background-image: url('https://profuturo.education/wp-content/uploads/2024/09/240911_PF-observatorio-IA-web.jpg');
      background-size: cover;
      background-position: center;
      color: #fff;
      overflow: hidden;
    }

    .free-learning-opportunities::before {
      content: "";
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      background-color: rgba(0, 0, 0, 0.7); 
      z-index: 0;
      border-radius: 12px; 
    }

    .free-learning-opportunities > * {
      position: relative;
      z-index: 1;
      text-shadow: 1px 1px 3px rgba(0,0,0,0.7);
    }

    /* Community-Based Interaction */
    .community-based-interaction {
      position: relative;
      background-image: url('https://miro.medium.com/v2/resize:fit:663/1*OPinZiZo3uaL03MMAPA9YQ.png');
      background-size: cover;
      background-position: center;
      color: #fff;
      overflow: hidden;
    }

    .community-based-interaction::before {
      content: "";
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      background-color: rgba(0, 0, 0, 0.7); 
      z-index: 0;
      border-radius: 12px; 
    }

    .community-based-interaction > * {
      position: relative;
      z-index: 1;
      text-shadow: 1px 1px 3px rgba(0,0,0,0.7);
    }

    /* User-Controlled Bartering */
    .user-controlled-bartering {
      position: relative;
      background-image: url('https://i.pinimg.com/736x/78/ea/fc/78eafc793659667a04012cc716c542ec.jpg');
      background-size: cover;
      background-position: center;
      color: #fff;
      overflow: hidden;
    }

    .user-controlled-bartering::before {
      content: "";
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      background-color: rgba(0, 0, 0, 0.7); /
      z-index: 0;
      border-radius: 12px; 
    }

    .user-controlled-bartering > * {
      position: relative;
      z-index: 1;
      text-shadow: 1px 1px 3px rgba(0,0,0,0.7);
    }

    /* Empowering Growth */
    .empowering-growth {
      position: relative;
      background-image: url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTdn8CsmqEMiL1Rp-QOBeVO9-1hmUtGPvc7bg&s');
      background-size: cover;
      background-position: center;
      color: #fff;
      overflow: hidden;
    }

    .empowering-growth::before {
      content: "";
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      background-color: rgba(0, 0, 0, 0.7); 
      z-index: 0;
      border-radius: 12px; 
    }

    .empowering-growth > * {
      position: relative;
      z-index: 1;
      text-shadow: 1px 1px 3px rgba(0,0,0,0.7);
    }
    
  </style>
</head>
<body>
  <section class="py-5 text-center">
    <div class="container">
      <h2>SKILLSWAP</h2>
      <div class="row g-4 justify-content-center">

        <!-- Skill Exchange System Card -->
        <div class="col-md-6 col-lg-4">
          <div class="service-card h-100 skill-exchange-card" 
              data-bg="https://media.istockphoto.com/id/1285788819/vector/experience-and-knowledge-exchange.jpg?s=612x612&w=0&k=20&c=85u-i0mtnp7bPXrY78huwZVA5VXXeyVFN6jjBjfqQcY=">
            <i class="fas fa-signal"></i>
            <div class="service-title">Skill Exchange System</div>
            <div class="service-text">
              SKILLSWAP is built around the concept of peer-to-peer skill sharing through a modern barter system. Users can offer their own skills—such as graphic design, cooking, coding, or photography—in exchange for a skill they want to learn. This structure removes the need for money in most learning interactions and creates a mutually beneficial learning environment where every user is both a student and a teacher.
            </div>
          </div>
        </div>

        <!-- Free Learning Opportunities -->
        <div class="col-md-6 col-lg-4">
          <div class="service-card h-100 free-learning-opportunities" 
              data-bg="https://profuturo.education/wp-content/uploads/2024/09/240911_PF-observatorio-IA-web.jpg">
            <i class="fas fa-signal"></i>
            <div class="service-title">Free Learning Opportunities</div>
            <div class="service-text">
              One of the core goals of SKILLSWAP is to provide accessible education by enabling users to learn new skills without financial cost. By trading time and knowledge instead of money, the platform ensures that anyone, regardless of their economic status, can continue developing their talents and gaining practical knowledge in a wide range of fields.
            </div>
          </div>
        </div>

        <!-- Community-Based Interaction -->
        <div class="col-md-6 col-lg-4">
          <div class="service-card h-100 community-based-interaction" 
            data-bg="https://miro.medium.com/v2/resize:fit:663/1*OPinZiZo3uaL03MMAPA9YQ.png">
            <i class="fas fa-signal"></i>
            <div class="service-title">Community-Based Interaction</div>
            <div class="service-text">
              To enhance collaboration and engagement, SKILLSWAP includes community spaces where users can connect based on the skills they offer or want to learn. These communities act as discussion groups or forums where members can share resources, post learning tips, ask questions, and support one another. This encourages long-term relationships and peer learning beyond individual skill swaps.
            </div>
          </div>
        </div>

        <!-- User-Controlled Bartering -->
        <div class="col-md-6 col-lg-4">
          <div class="service-card h-100 user-controlled-bartering" 
            data-bg="https://i.pinimg.com/736x/78/ea/fc/78eafc793659667a04012cc716c542ec.jpg">
            <i class="fas fa-signal"></i>
            <div class="service-title">User-Controlled Bartering</div>
            <div class="service-text">
              Each user has full autonomy over the exchange process. When a barter request is received, the user can review the details—including the skill offered, the skill requested, and the user's profile—before choosing to accept or decline. This system gives users the freedom to manage their own learning journey and ensures that exchanges only happen when both parties agree.
            </div>
          </div>
        </div>

        <!-- Empowering Growth -->
        <div class="col-md-6 col-lg-4">
          <div class="service-card h-100 empowering-growth" 
            data-bg="https://pulsehrm.com/wp-content/uploads/2023/08/4.-Staffing-Excellence-Empowering-Your-Companys-Growth.png">
            <i class="fas fa-signal"></i>
            <div class="service-title">Empowering Growth Through Connection</div>
            <div class="service-text">
              At its core, SKILLSWAP is designed to foster personal and collective growth. By creating a space where people can share their abilities, gain new knowledge, and connect with others who have similar goals, the platform encourages users to build confidence, discover new opportunities, and support one another in their learning journeys.
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <script>
  const cards = document.querySelectorAll('.service-card');
  const body = document.body;

  cards.forEach(card => {
    card.addEventListener('mouseenter', () => {
      const bg = card.getAttribute('data-bg');
      body.style.backgroundImage = `url(${bg})`;
      body.classList.add('hovered-background');
    });

    card.addEventListener('mouseleave', () => {
      body.style.backgroundImage = '';
      body.classList.remove('hovered-background');
    });
  });
</script>

</body>
</html>
