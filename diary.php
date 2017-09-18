<!DOCTYPE html>
<html>
    <head>
        <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
        <script type="text/javascript" src="https://unpkg.com/vue@2.4.3/dist/vue.js"></script>
        <script type="text/javascript" src="diaryComponents.js"></script>
        <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/moment@2.18.1/moment.min.js"></script>
          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css">
           <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js"></script>
            <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" href="diary-styles.css" type="text/css" />
    </head>
    <body>
    <div id="main">
    <nav>
    <div class="nav-wrapper">
      <a @click="switchMode('index')" href="#" class="brand-logo center">Secret Diary <i id="book" style="font-size:44px !important;" class="material-icons">book</i></a>
      <a v-if="currentView === 'diary-list'" href="#" data-activates="slide-out" class="button-collapse left"><i class="material-icons">menu</i></a>
      <ul class="right">
        <li><a @click="switchMode('new')" class="full" href="#" id="new">New diary</a></li>
        <li><a class="full" href="#" id="logout" @click="logout">Logout</a></li>
      </ul>
    </div>
  </nav>

    <div id="diary" class="container">
        <div class="row">
        <div class="col s12">
        <div v-if="!loggedIn">
            <h1>Sorry, you have to be logged in to see this page. Redirecting...</h1>
        </div>
        <div v-else>
            <component @filtered="filter" :side-bar-criteria="criteria" :post-id="postId" @edit-current-post="editCurrentPost" :is="currentView">
            </component>
            <span v-if="currentView === 'diary-list'" class="side-nav center" id="slide-out">
                <a @click="filter('newest',$event)" class='waves-effect btn' style="background-color:#8e24aa;" href='#'>Newest</a>
                <a @click="filter('oldest',$event)" class='waves-effect btn' style="background-color:gray;" href='#'>Oldest</a>
                <a @click="filter('title',$event)"  class='waves-effect btn' style="background-color:gray;" href='#'>Title</a>
            </span>  
        </div>
        </div>
        </div>
    </div>
    </div>
    </body>
<script type="text/javascript">

window.onresize = function() {
if(window.innerWidth <= 600) {
    document.querySelector("#new").innerHTML = "<i class='material-icons'>add<i> ";
    document.querySelector("#logout").innerHTML = "<i class='material-icons'>close<i> ";
    document.querySelector(".brand-logo").style.fontSize = "16px";
    $('#book').css('fontSize', '24px');
} else {
    document.querySelector("#new").innerHTML = "New diary";
    document.querySelector("#logout").innerHTML = "Logout";
    document.querySelector(".brand-logo").style.fontSize = "32px";
    $('#book').css('fontSize', '24px');
}
}

    const app = new Vue({
       el: '#main',
       data: {
           loggedIn: sessionStorage.getItem("user") || localStorage.getItem("user"),
           currentView: 'diary-list',
           postId: -1,
           criteria: ''
       },
       methods: {
           logout(){
               sessionStorage.removeItem("remember");
               sessionStorage.removeItem("user");
               window.location = "index.php";
           },
           async switchMode(mode) {
               switch(mode) {
                   case "new":
                        let data = await axios.post('diaryController.php',{newpost: true, email: this.loggedIn});
                        this.postId = data.data;
                       this.currentView = 'diary-form';
                       break;
                   case "index":
                       this.currentView = "diary-list";
                       break;
               }
           },
           filter(criteria,event) {
             $(event.target).css("backgroundColor","#8e24aa");
             $(event.target).siblings().css("backgroundColor","gray");
             this.criteria = criteria;
           },
           editCurrentPost(value) {
               this.currentView = 'diary-form';
               this.postId = value;
           }
       },
       created() {
           console.log(sessionStorage.getItem("user"));
        if(!this.loggedIn) {
          setTimeout(function(){
            window.location = "index.php";
          }, 1500);
        }
       }
    });

    $(".button-collapse").sideNav();
</script>
</html>