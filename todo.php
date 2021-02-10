<?php
require "funcs.php";
session_start();

// calender.phpにてクリックされた日時(date)をcookieから取得
if(isset($_COOKIE['date'])){
  $date_ = json_decode($_COOKIE["date"]);
  $_SESSION['date'] = $date_;
}
// 3つの情報をsessionから取得
$date = $_SESSION['date'];
$user_id = $_SESSION['id'];
$name = $_SESSION['name'];

// cookieにdateデータがある場合、SQLからデータを取ってくる
if(isset($_COOKIE['date'])){
  $pdo = db_conect();
  $prepare_1 = $pdo->prepare("SELECT * FROM works WHERE user_id = ? AND date = ?");
  $prepare_1->bindValue(1, $user_id, PDO::PARAM_INT);
  $prepare_1->bindValue(2, $date, PDO::PARAM_STR);
  $status_1 = $prepare_1->execute();

  $info_array = array();
  if($status_1==false){
    //SQL実行時にエラーがある場合(エラーオブジェクト取得して表示)
    $error = $prepare_1->errorInfo();
    exit("QueryError:".$error[2]);
  }else{
    //Selectデータの数だけ自動でループしてくれる
    while( $result = $prepare_1->fetch()){
      array_push($info_array, $result['task']);
      array_push($info_array, $result['start_time']);
    }
  }
  $info_json = json_encode($info_array);
}else{
  $info_json = json_encode('nothing');
}


// 追加処理
// todo.phpにおいて、taskとtimeがcookieに保存された場合、SQLに書き込む
if(isset($_COOKIE['task']) && isset($_COOKIE['time'])){
  $task = $_COOKIE["task"];
  $start_time = $_COOKIE["time"];
  $pdo = db_conect();
  $sql = "INSERT INTO works(id, user_id, date, start_time, task) VALUES (NULL, :user_id, :date, :start_time, :task)";
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
  $stmt->bindValue(':date', $date, PDO::PARAM_STR);
  $stmt->bindValue(':start_time', $start_time, PDO::PARAM_STR);
  $stmt->bindValue(':task', $task, PDO::PARAM_STR);
  $status = $stmt->execute();
  if($status==false){
    //SQL実行時にエラーがある場合(エラーオブジェクト取得して表示)
    $error = $stmt->errorInfo();
    exit("QueryError:".$error[2]);
    // exit('<a href="index.html">ログイン画面へ</a>');
  }else{
  }
}else{
  $task = '';
  $start_time = '';
}

// 削除処理
// todo.phpにおいて、del_taskとdel_timeがcookieに保存された場合、SQLから該当レコードを削除
if(isset($_COOKIE['del_task']) && isset($_COOKIE['del_time'])){
  $del_task = $_COOKIE["del_task"];
  $del_start_time = $_COOKIE["del_time"];
  $pdo = db_conect();
  $sql = "DELETE FROM works WHERE user_id = ? AND date = ? AND start_time = ? AND task = ?";
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
  $stmt->bindValue(2, $date, PDO::PARAM_STR);
  $stmt->bindValue(3, $del_start_time, PDO::PARAM_STR);
  $stmt->bindValue(4, $del_task, PDO::PARAM_STR);
  $status = $stmt->execute();
  if($status==false){
    //SQL実行時にエラーがある場合(エラーオブジェクト取得して表示)
    $error = $stmt->errorInfo();
    exit("QueryError:".$error[2]);
  }else{
  }
}else{
  $del_task = '';
  $del_start_time = '';
}

// 完了タスク全て削除処理
// todo.phpにおいて、delete_tasksがcookieに保存された場合、SQLから該当レコード達を削除する
if(isset($_COOKIE['delete_tasks'])){
  $delete_tasks = json_decode($_COOKIE["delete_tasks"], TRUE);
  $kanma = '"';
  $pdo = db_conect();

  for($i=0; $i<count($delete_tasks); $i++){
    $sql = "DELETE FROM works WHERE user_id = ? AND date = ? AND start_time = ? AND task = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
    $stmt->bindValue(2, $date, PDO::PARAM_STR);
    $stmt->bindValue(3, json_encode($delete_tasks[$i]['Start']), PDO::PARAM_STR);
    $stmt->bindValue(4, $kanma.$delete_tasks[$i]['Content'].$kanma, PDO::PARAM_STR);

    $status = $stmt->execute();
    if($status==false){
      //SQL実行時にエラーがある場合(エラーオブジェクト取得して表示)
      $error = $stmt->errorInfo();
      exit("QueryError:".$error[2]);
    }else{
    }
  }
}else{
  $delete_tasks = '';
}

// 編集処理
// todo.phpにおいて、edit_taskとedit_timeとnew_taskがcookieに保存された場合、SQLから該当レコードのtaskを変更
if(isset($_COOKIE['edit_task']) && isset($_COOKIE['edit_time']) && isset($_COOKIE['new_task'])){
  $edit_task = $_COOKIE["edit_task"];
  $edit_time = $_COOKIE["edit_time"];
  $new_task = $_COOKIE["new_task"];
  $pdo = db_conect();

  $sql = "UPDATE works SET task = :new_task WHERE user_id = :user_id AND date = :date AND start_time = :start_time AND task = :task";
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':new_task', $new_task, PDO::PARAM_STR);
  $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
  $stmt->bindValue(':date', $date, PDO::PARAM_STR);
  $stmt->bindValue(':start_time', $edit_time, PDO::PARAM_STR);
  $stmt->bindValue(':task', $edit_task, PDO::PARAM_STR);
  $status = $stmt->execute();
  if($status==false){
    //SQL実行時にエラーがある場合(エラーオブジェクト取得して表示)
    $error = $stmt->errorInfo();
    exit("QueryError:".$error[2]);
  }else{
  }
}else{
  $edit_task = '';
  $edit_time = '';
  $new_task = '';
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My todo</title>
  <link rel="stylesheet" href="css/todo.css">
</head>
<body>
  <div id="app">
    <div class="header">
      <!-- <a href="todo.php" @click="movePrevDay" class='moveday'>< Prev Day</a>
      <a href="todo.php" @click="moveNextDay" class='moveday'>Next Day ></a> -->
      <!-- <p class="" @click="movePrevDay">< Prev Day</p> -->
      <h1>My To Do List at <span>< {{selectday}} ></span></h1>
      <!-- <p class="" @click="moveNextDay">Next Day ></p> -->
    </div>

    <form @submit='addItem' class="addItem" action='todo.php'>
      <!-- <p>・内容と開始時刻を記入し追加して下さい</p> -->
      <input type="text" v-model='newItem' id="text">
      <input type="time" v-model='newTime' id="time">
      <input type="submit" value="追加" class="btn addbtn">
    </form>

    <div class="above_table">
      <div class="radio_box">
        <label v-for='opt in option' class="radio">
          <input type="radio" v-model='current' :value='opt.Filt'>{{opt.Label}}
        </label>
        <button @click='timesort_early' class="btn sort">昇順</button>
        <button @click='timesort_late' class="btn sort">降順</button>
      </div>
      <div class="container">
        <!-- <a href="#" @click='purgeItem' class="btn alldelete"><span>完了タスクを全て削除<span></a> -->
      </div>
    </div>

    <table>
      <tr class="head">
        <th class="No">No</th>
        <th class="content">内容</th>
        <th class="start">開始時刻</th>
        <th class="state">状態</th>
        <th class="edit_delete">
          <div class="container">
            <!-- <a href="#" class="btn alldelete"> -->
            <a href="todo.php" @click='purgeItem' class="btn alldelete">
              <span>完了タスクを全て削除<span>
            </a>
          </div>
        </th>
      </tr>
      <tr v-for='(todo,index) in filtItem' class="main" :class='{fin_main:todo.State}'>
        <td class='left'>{{index+1}}</td>
        <td :class='{fin_cont:todo.State}' class='left'>{{todo.Content}}</td>
        <td class='left'>{{todo.Start}}</td>
        <td>
          <input type="button" value="完了" v-if='todo.State' @click='turnItem(index)' class="btn btn_table">
          <input type="button" value="作業中" v-else @click='turnItem(index)' class="btn btn_table right">
        </td>
        <td>
          <a href="todo.php" @click='editItem(index)' class="btn btn_table right most_right">編集</a>
          <a href="todo.php" @click='deleteItem(index)' class="btn btn_table right most_right">削除</a>
        </td>
      </tr>
    </table>
  </div>
  <!-- <h1 class="deletedesc">❇︎ 削除ボタンは「shift」キーを押しながらクリックして下さい</h1> -->
  <h1 class="deletedesc"></h1>
  <a class="returncal" href="calender.php">カレンダーに戻る</a>
  <a class="returncal" href="logout.php">ログアウト</a>
  
  <script src="https://cdn.jsdelivr.net/npm/vue"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <!-- <script src="js/todo.js"></script> -->
  <script>
    console.log("<?= $date ?>");
    console.log("<?= $user_id ?>");
    console.log("<?= $name ?>");

    info_array = <?= $info_json ?>;

    var vm = new Vue({
      el: '#app',

      data: {
        selectday:[],
        newItem:[],
        newTime:[],
        todos:[],
        option:[
          {
            'Filt': 'both',
            'Label': '全て'
          },
          {
            'Filt': false,
            'Label': '作業中'
          },
          {
            'Filt': true,
            'Label': '完了'
          }
        ],
        current: 'both'
      },

      watch: {
        todos: {
          handler: function(){
            localStorage.setItem(this.selectday, JSON.stringify(this.todos));
          },
          deep:true
        },
        selectday: function(){
          localStorage.setItem('selectedDay', this.selectday)
          this.todos = JSON.parse(localStorage.getItem(this.selectday)) || [];
        }
      },
      mounted: function(){
        this.selectday = localStorage.getItem('selectedDay');
        if(info_array != 'nothing'){
          for(i=0; i<info_array.length;i=i+2){
            var content = info_array[i].replace('"','');
            var content = content.replace('"','');
            var start = info_array[i+1].replace('"','');
            var start = start.replace('"','');
            dict = {
              'Content':content,
              'Start':start,
              'State':false
            }
            this.todos.push(dict);
          }
        }else{
          this.todos = JSON.parse(localStorage.getItem(this.selectday)) || [];
        }
        
      },

      methods: {
        addItem: function(){
          var item = {
            'Content':this.newItem,
            'Start':this.newTime,
            'State':false
          };
          this.todos.push(item);
          //追加itemをcookieに送る
          var age = 1;
          document.cookie = 'task=' + JSON.stringify(this.newItem) + ";max-age=" + age;
          document.cookie = 'time=' + JSON.stringify(this.newTime) + ";max-age=" + age;

          this.newItem = '';
          this.newTime = '';
        },
        deleteItem: function(index){
          //削除itemをcookieに送る
          const del_task = this.todos[index]['Content'];
          const del_time = this.todos[index]['Start'];
          var age = 1;
          document.cookie = 'del_task=' + JSON.stringify(del_task) + ";max-age=" + age;
          document.cookie = 'del_time=' + JSON.stringify(del_time) + ";max-age=" + age;
          //this.todosから削除すると同時にlocalstorageに保存したものも変更する
          this.todos.splice(index,1);
        },
        editItem: function(index){
          var item = prompt(this.todos[index].Content,this.todos[index].Content);
          if(item == null){
            return;
          }else{
            //削除itemをcookieに送る
            const edit_task = this.todos[index]['Content'];
            const edit_time = this.todos[index]['Start'];
            var age = 1;
            document.cookie = 'edit_task=' + JSON.stringify(edit_task) + ";max-age=" + age;
            document.cookie = 'edit_time=' + JSON.stringify(edit_time) + ";max-age=" + age;
            document.cookie = 'new_task=' + JSON.stringify(item) + ";max-age=" + age;
            this.todos[index].Content = item;
          }
        },
        turnItem: function(index){
          this.todos[index].State = !this.todos[index].State;
        },
        purgeItem: function(){
          if(!confirm('完了タスクを全て消して良いですか?')){
            return;
          };
          delete_list = this.todos.filter(function(el){
            return el.State;
          })
          // 完了状態のタスクのリストをcookieへ送る
          var age = 1;
          document.cookie = 'delete_tasks=' + JSON.stringify(delete_list) + ";max-age=" + age;
          this.todos = this.remaining;
        },
        movePrevDay: function(){
          var arr = this.selectday.split("/");
          var now_date = new Date(arr[0], arr[1]-1, arr[2]);
          now_date.setDate(now_date.getDate()-1);
          this.selectday = now_date.getFullYear() + '/' + (now_date.getMonth()+1) + '/' + now_date.getDate();
        },
        moveNextDay: function(){
          var arr = this.selectday.split("/");
          var now_date = new Date(arr[0], arr[1]-1, arr[2]);
          now_date.setDate(now_date.getDate()+1);
          this.selectday = now_date.getFullYear() + '/' + (now_date.getMonth()+1) + '/' + now_date.getDate();
        },
        timesort_early: function(){
          this.todos.sort(function(a,b){
            return (a.Start > b.Start ? 1: -1);
          });
        },
        timesort_late: function(){
          this.todos.sort(function(a,b){
            return (a.Start < b.Start ? 1: -1);
          });
        }
      },

      computed: {
        filtItem: function(){
          if(this.current == true){
            return this.todos.filter(function(el){
              return el.State;
            });
          }else if(this.current == false){
            return this.todos.filter(function(el){
              return !el.State;
            });
          }else{
            return this.todos;
          }
        },
        remaining: function(){
          return this.todos.filter(function(el){
            return !el.State;
          });
        }
      }
    })
    $('.returncal').on("click", function(){
      localStorage.clear();
    })
  </script>
</body>
</html>