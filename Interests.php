<!DOCTYPE html>
 <html lang="en">
 <head>
   <meta charset="UTF-8">
   <title>Interests</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
   <!-- SweetAlert2 -->
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   <style>
     body {
       background-color: #f6ff00;
       font-family: 'Segoe UI', sans-serif;
     }
 
     .header {
       position: absolute;
       top: 20px;
       left: 20px;
       display: flex;
       align-items: center;
       gap: 10px;
     }
 
     .logo-img {
       height: 60px;
       width: auto;
     }
 
     .site-name {
       font-weight: bold;
       font-size: 30px;
       color: black;
       text-decoration: none;
     }
 
     .site-name:hover {
     text-decoration: none;
     }
 
     .interest-container {
       background: rgba(255, 255, 255, 0.95);
       padding: 40px;
       border-radius: 12px;
       box-shadow: 0 0 20px rgba(0,0,0,0.3);
       max-width: 600px;
     }
 
     .interest-btn {
       background-color: #fefcbf;
       border: 1px solid #4a4a4a;
       font-weight: bold;
       padding: 15px;
       border-radius: 12px;
       width: 100%;
       transition: 0.2s ease-in-out;
     }
 
     .interest-btn:hover {
       background-color: #f6f65a;
       transform: scale(1.03);
     }
 
     .interest-btn.active {
       background-color: #f6f65a !important;
       border-color: #4a4a4a;
       color: #000;
       box-shadow: 0 0 10px rgba(0,0,0,0.15);
     }
 
     .btn-confirm {
       background-color: #f6f65a;
       font-weight: bold;
       border-radius: 12px;
       padding: 10px 30px;
       margin-top: 20px;
       transition: 0.2s;
     }
 
     .btn-confirm:hover:enabled {
       background-color: #ffff77;
       transform: scale(1.03);
     }
   </style>
 </head>
 <body>
   <div class="header">
     <img src="assets/images/sslogo.png" alt="Logo" class="logo-img">
     <a href="Dashboard.php" class="site-name">SkillSwap</a>
   </div>
 
   <div class="container d-flex justify-content-center align-items-center vh-100">
     <div class="interest-container text-center w-100">
       <h2 class="mb-4">What are you looking for?</h2>
       <form method="POST" action="home.php">
         <div class="row g-3">
           <div class="col-md-6">
             <button type="button" class="interest-btn"
               onclick="document.querySelectorAll('.interest-btn').forEach(b=>b.classList.remove('active')); this.classList.add('active'); document.getElementById('confirmBtn').disabled = false;">
               Arts & Creativity
             </button>
           </div>
           <div class="col-md-6">
             <button type="button" class="interest-btn"
               onclick="document.querySelectorAll('.interest-btn').forEach(b=>b.classList.remove('active')); this.classList.add('active'); document.getElementById('confirmBtn').disabled = false;">
               Beauty & Hair
             </button>
           </div>
           <div class="col-md-6">
             <button type="button" class="interest-btn"
               onclick="document.querySelectorAll('.interest-btn').forEach(b=>b.classList.remove('active')); this.classList.add('active'); document.getElementById('confirmBtn').disabled = false;">
               Business & Professional
             </button>
           </div>
           <div class="col-md-6">
             <button type="button" class="interest-btn"
               onclick="document.querySelectorAll('.interest-btn').forEach(b=>b.classList.remove('active')); this.classList.add('active'); document.getElementById('confirmBtn').disabled = false;">
               Caretaking & Sitting
             </button>
           </div>
         </div>
         <button id="confirmBtn" type="submit" class="btn btn-confirm mt-4" disabled>Confirm</button>
       </form>
     </div>
   </div>



    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
 </body>
 </html>