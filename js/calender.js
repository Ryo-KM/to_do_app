
var vm_1 = new Vue({
	el:"#app_1",
  data:{
    today:"",
    selectedDay:"",
    currentYear:0,
    currentMonth:0,
    currentDate:0,
    weeks:["月","火","水","木","金","土","日"],
    calender:[],
    holidays:[]
  },
  created(){
    // カレンダーを開いた当日の日付を取得する
    const date  = new Date();
    [this.currentYear,  this.currentMonth, this.currentDate] = [date.getFullYear(), date.getMonth() + 1, date.getDate()];
    // console.log(('0' + this.currentMonth).slice(-2));
    this.today = this.selectedDay = `${this.currentYear}-${('0' + this.currentMonth).slice(-2)}-${this.currentDate}`;
    // console.log(this.today);
  
  },
  methods:{
    // 1月→12月、12月→1月の移動の際は例外処理が働く様にする。
    movePrevMonth(){
      this.currentMonth = this.currentMonth != 1 ? this.currentMonth - 1 : 12;
      this.currentYear = this.currentMonth != 12 ? this.currentYear : this.currentYear - 1;
    },
    moveNextMonth(){
      this.currentMonth = this.currentMonth != 12 ? this.currentMonth + 1 : 1;
      this.currentYear = this.currentMonth != 1 ? this.currentYear : this.currentYear + 1;
    },
    // 押した日にちを'selectedDay'の値として、localstorageに保存する
    addDay: function(day){
      this.selectedDay = this.currentYear + '/' + this.currentMonth + '/' + day;
      // 追加処理
      // クリックした日にちをcookieに保存する
      var x = this.selectedDay;
      var age=5;
      document.cookie = 'date=' + JSON.stringify(x) + ";max-age=" + age;
      localStorage.setItem('selectedDay', this.selectedDay);
    }
  },
  computed:{
    calenderMake(){
      // currentYearとcurrentMonthから、その年・月の1日の曜日と最終日が何日かを取得
      const firstday = new Date(this.currentYear, this.currentMonth - 1, 1).getDay();
      const lastdate = new Date(this.currentYear, this.currentMonth, 0).getDate();
      // 1日の曜日が日曜(数値としては0)ならば、スペースは6。それ以外なら数値-1をする
      const necessarySpace = firstday == 0 ? 6 : firstday - 1;
      // スペースだけのリストの要素数をnecessarySpaceの数と同じにする。値は全て" "空にする
      const space_list = Array(necessarySpace).fill(" ");
      // lastdateの数だけリストの要素数を作り、全て1を入力。そこにindex+1を掛け、1,2,3,,,,という配列を作成
      const day_list = Array(lastdate).fill(1).map(function(value, index){
        return value * (index + 1);
      });
      // 2つの配列を合成する
      Array.prototype.push.apply(space_list, day_list);
      // console.log(space_list);
      return space_list;
    },
  }
});

$('.logout').on("click", function(){
  localStorage.clear();
})