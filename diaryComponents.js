const diaryList = Vue.component('diary-list',{
    template: `
    <ul class="collection with-header">
        <li class="collection-header">
            <h4 style="margin:0 auto;"><span style="font-family:'Bad Script;color:#8e24aa;'">Your diaries</span>
             
              <span style="float:right;" class="hide-on-med-and-down" id="sort">
                <a @click="filter('newest',$event)" class='waves-effect btn' style="background-color:#8e24aa;" href='#'>Newest</a>
                <a @click="filter('oldest',$event)" class='waves-effect btn' style="background-color:gray;" href='#'>Oldest</a>
                <a @click="filter('title',$event)" class='waves-effect btn' style="background-color:gray;" href='#'>Title</a>
              </span>
            </h4>
        </li>
        
        <diary-entry @deletePost="removePost" @editPost="editPost" v-for="entry in entries" :key="entry.id" :title="entry.title" :date="entry.created" :id="entry.id" :email="entry.user_email"></diary-entry>
        
    </ul>
    `,
    methods: {
      filter(criteria,event) {
          $(event.target).css("backgroundColor","#8e24aa");
          $(event.target).siblings().css("backgroundColor","gray");
          switch(criteria) {
              case "newest":
                  return this.entries.sort((a,b) => {
                      return new Date(b.created) - new Date(a.created);
                  });
              case "oldest":
                  return this.entries.sort((a,b) => {
                      return new Date(a.created) - new Date(b.created);
                  }); 
              case "title":
                  return this.entries.sort((a,b) => {
                      return a.title > b.title;
                  });
          }
          this.$emit('filtered',criteria);
      },
      editPost(value){
          this.$emit('edit-current-post',value);
      },
      async removePost(value) {
          for(let entry of this.entries) {
              if(entry.id === value) {
                  this.entries.splice(this.entries.indexOf(entry), 1);
              }
          }
        let deletePost = await axios.post('diaryController.php',{deletePost: true, postId: value,email: sessionStorage.getItem("user") || localStorage.getItem("user")});  
      }
    },
    data() {
        return {
            entries: [],
            criteria: ''
        };
    },
    props: ['sideBarCriteria'],
    async created(){
        let {data} = await axios.post('diaryController.php',{fetchPosts: true, email: sessionStorage.getItem("user") || localStorage.getItem("user")});
        this.entries = data;
    }
});

Vue.component('diary-entry',{
   template: `
    <li class="collection-item">
        <h5>
          <span :title="getDate">{{truncateTitle}}</span>
          <a href="#!" class="secondary-content">
            <i @click="editPost" class="material-icons">edit</i>
            <i @click="deletePost" class="material-icons">delete</i>
          </a>
        </h5>
    </li>
   `,
   props: ['title','date','id','email'],
   methods: {
       editPost() {
           this.$emit('editPost',this.id);
       },
       deletePost() {
           if(confirm("Are you sure you want to delete this diary entry?"))
           this.$emit('deletePost',this.id);
       }
   },
   computed: {
       getDate() {
           return "Created on: " + moment(this.date).format("MMMM Do, YYYY");
       },
       truncateTitle() {
           if(this.title.length > 80) {
            let count = 0;
            return this.title.split(" ").map(s => {
              if(count >= (75)) {
                return;
              }
              count += s.length;
              return s;
            }).filter(s => s !== undefined).join(" ") + "...";
          }
          return this.title;
       }
   }
});
//no more than 80 chars

Vue.component('diary-form',{
    template: `
        <div style="margin-top: 5%">
        <h5 class="center">{{changes ? "Saving..." : "All changes saved"}}</h5>
          <input @keyup="edit('title')" v-model="title" id="title" placeholder="Title">
    
         <div class="input-field col s12">
          <textarea @keyup="edit('content')" v-model="content" id="textarea1" class="materialize-textarea"></textarea>
          <label for="textarea1">Your Diary Post</label>
        </div>
        </div>
    `,
    data() {
      return {
          title: '',
          content: '',
          timeout: null,
          changes: false
      };  
    },
    methods: {
        edit(field) {
            this.changes = true;
            let vue = this;
            clearTimeout(this.timeout);
            this.timeout = setTimeout(async function(){
               let change = field === "title" ? "changeTitle" : "changeContent";
               let {data} = await axios.post('diaryController.php',
               {changeField: change,title:vue.title,content: vue.content,postId: vue.postId,email: sessionStorage.getItem("user") || localStorage.getItem("user")});
               vue.changes = false;
            },300);
        }
    },
    props: ['post-id'],
    async created() {
         if(this.postId > 0) {
          let {data} = await axios.post('diaryController.php',{editCurrentPost: true, postId: this.postId, email: sessionStorage.getItem("user") || localStorage.getItem("user")});
          this.title = data.title;
          this.content = data.content;
         } 
    }
});