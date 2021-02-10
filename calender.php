<?php
session_start();
$id = $_SESSION['id'];
$name = $_SESSION['name'];
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Calender</title>
  <link rel="stylesheet" href="css/calender.css">
</head>
<body>
  <div id="app_1">
    <!-- <span class="btn-modal-close" @click="close($event)"></span> -->
    <h1>
      <?=$name?>の予定表
    </h1>

    <div class="calender-title">
      <p class="btn-monthMove" @click="movePrevMonth">< Prev Month</p>
      <h1>- {{currentYear+"/"+currentMonth}} -</h1>
      <p class="btn-monthMove" @click="moveNextMonth">Next Month ></p>
    </div>

    <div class="calender-body">
      <div class="calender-body__item week_name">
        <div v-for="day in weeks" class="day">{{day}}</div>
      </div>
      <div class="calender-body__item week_day">

        <!-- <form action="todo.php" method='post'>
          <input type="text" >
        </form> -->

        <a v-for="day in calenderMake" :value="day" href="todo.php" @click='addDay(day)' id="day">{{day}}</a>
      </div>
    </div>

    <p class="login"><a href="logout.php" class="logout">ログアウト</a></p>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/vue"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="js/calender.js"></script>
</body>
</html>