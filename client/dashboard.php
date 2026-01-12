<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['tipo'] !== 'C') {
    header("Location: /ProyectoIngWeb/pages/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Homepage | Jav-a Coffe</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>

<body class="bg-gray-100 inline justify-center h-screen">

<!-- Header -->
  <?php include 'includes/header.php'; ?>

  <!-- Hero Section -->
  <section class="relative bg-cover bg-center h-[80vh]"
    style="background-image: url('https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=1600&q=80');">
    <div
      class="absolute inset-0 bg-gray-950 bg-opacity-60 flex flex-col justify-center items-center text-center text-white p-6">
      <h2 class="text-4xl md:text-6xl font-bold mb-4">“Code your day with good vibes and great flavor.”</h2>
      <p class="text-lg md:text-xl max-w-2xl">
        En Jav-A Coffee creemos que el bienestar se programa con una buena taza y una mente tranquila. Aquí mezclamos
        comida deliciosa, café de especialidad y momentos de calma mental.
      </p>
    </div>
  </section>

  <!-- Cuerpo -->
  <section id="cuerpo" class="py-16 px-6 md:px-20">
    <div class="grid md:grid-cols-3 gap-8">
      <div class="bg-emerald-100 p-6 rounded-2xl shadow-md hover:shadow-xl transition">
        <img src="https://images.unsplash.com/photo-1510626176961-4b57d4fbad03"
          class="rounded-xl mb-4 w-full h-48 object-cover">
        <p class="italic text-lg">“Un café caliente y una charla honesta pueden sanar el alma.”</p>
      </div>
      <div class="bg-emerald-100 p-6 rounded-2xl shadow-md hover:shadow-xl transition">
        <img src="https://images.unsplash.com/photo-1528715471579-d1d0e5ab1b1a"
          class="rounded-xl mb-4 w-full h-48 object-cover">
        <p class="italic text-lg">“Tu cuerpo merece alimentos que te hagan sentir bien, no solo llenarte.”</p>
      </div>
      <div class="bg-emerald-100 p-6 rounded-2xl shadow-md hover:shadow-xl transition">
        <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836"
          class="rounded-xl mb-4 w-full h-48 object-cover">
        <p class="italic text-lg">“Tómate un respiro. Cada sorbo puede ser un nuevo comienzo.”</p>
      </div>
    </div>
  </section>

  <?php include 'includes/footer.php'; ?>

  <!-- Activar iconos Lucide -->
  <script src="https://unpkg.com/lucide-icons/dist/umd/lucide.js"></script>
  <script> lucide.createIcons(); </script>

  <?php include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/client/includes/logoutModal.php'; ?>
  
</body>

</html>