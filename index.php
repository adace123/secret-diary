   <?php include('header.php'); ?>
   <div v-if="!loggedIn">
       <h1>Secret Diary</h1>
    <p>Store your thoughts permanently and securely.</p>
    
    <div v-if="errors.length > 0" class="alert alert-danger align-middle" role="alert">
        There were error(s) in your form:<br>
        <span v-for="error in errors">{{error}}<br></span>
    </div>
    
    <p style="font-weight: normal;">{{!registerMode ? 'Log in using your username and password.' : 'Interested? Sign up now.'}}</p>
    <form @submit.prevent="validateForm" id="form">
      <div class="form-group">
        <input v-model="email" name="email" type="text" class="form-control" placeholder="Email">
      </div>
      <div class="form-group">
        <input v-model="password" name="password" class="form-control" type="password" placeholder="Password"></input>
      </div>
        <div class="form-group">
          <input @change="remember = !remember" type="checkbox">
          <label  for="remember">Stay logged in</label>
          
        </div>
        <button type="submit" name="submit" class="btn btn-success">Submit</button>
    </form>
     <div style="margin-top: 20px">
       <a href="#" @click="switchMode">{{registerMode ? 'Log in' : 'Register'}}</a>
     </div>
    
   </div>
       
  <div v-else>
      <h1>Whoops, looks like you're already logged in. Redirecting...</h1>
  </div>
      <?php include('footer.php'); ?>
   
  
     
<script type="text/javascript">

    if(sessionStorage.getItem("remember") === "true") {
        setTimeout(function(){window.location.href = "diary.php";}, 1500);
    } else {
        sessionStorage.removeItem("remember");
        sessionStorage.removeItem("user");   
        localStorage.removeItem("user");
    }
    new Vue({
       el: '#container',
       methods: {
          validateForm() {
             this.errors = [];
             if(this.email === '') {
                 this.errors.push("Email cannot be blank");
             } 
             if(this.password === '') {
                 this.errors.push("Password cannot be blank");
             } 
             if(this.email.length > 0 && !/[\.-\w+]+@[\.-\w+]\w+\.\w+/g.test(this.email)) {
                 this.errors.push("Invalid email");
             } 
             if(this.password.length > 0 && this.password.length < 8) {
                 this.errors.push("Password must be at least 8 characters.");
             }
            
             if(this.errors.length === 0) {
                 this.sendForm();
             }
             
          },
            async sendForm() {
              this.errors = [];
              
              let {data} = await axios.post('form-handler.php',
                  {email: this.email, password: this.password, registerMode: this.registerMode});
                  console.log(data);
              if(data.status === "success") {
                sessionStorage.setItem("remember", this.remember);
                if(sessionStorage.getItem("remember") === "true")
                localStorage.setItem("user", this.email);
                else sessionStorage.setItem("user", this.email);
                window.location = 'diary.php';   
                 return;
              }
              this.errors.push(this.registerMode ? "Sorry, that email is already registered" : "Sorry, that email and password combo is invalid.");
          },
          switchMode() {
             this.errors = [];
             this.registerMode = !this.registerMode;
          }
       }, 
       data: {
          email: '',
          password: '',
          registerMode: true,
          remember: false,
          errors: []
       },
       computed: {
           loggedIn() {
               return sessionStorage.getItem("user") !== null;
           }
       },
       created() {
           console.log(sessionStorage.getItem("remember") === "true")
       }
    });
</script>
    </body>
</html>