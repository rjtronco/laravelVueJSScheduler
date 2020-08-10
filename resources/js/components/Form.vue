<template>
    <div  class="row w-100 margin-5">  
        <div id="snackbar">text</div>
        <form @submit.prevent="validateForm" id='formCom' class="col">
            <div class="form-group">
                <label for="title" class="col-sm-2 col-form-label">Title</label>
                <div class="col-sm-12">
                    <input v-model= "form.name" type="text" class="form-control" id="name" placeholder="Title">
                </div>
            </div>
            <div class="form-group row">
                <div class="col">
                    <label for="date1" class="col-lg-2 col-form-label">From</label>
                    <div class="m-2 col-sm-10">
                        <input v-model= "form.dateFrom" type="date" min = "2020-08-01" max = "2020-08-31" class="form-control" id="date1" placeholder="Title">
                    </div>
                </div>
                <div class="col">
                    <label for="date2" class="col-lg-2 col-form-label">To</label>
                    <div class="col-sm-10">
                        <input type="date"  v-model= "form.dateTo" min = "2020-08-01" max = "2020-08-31" class="form-control" id="date2" placeholder="Title">
                    </div>
                </div>
            </div>
             <div class="form-row">
                <div class="form-col form-check" v-for="(day,index) in Days" v-bind:key="day.name">
                    <label class="form-check-label" :for="(index)">{{day.name}}</label>
                    <input type="checkbox" v-model="form.checkedDays" :id="day.name" :value="day.name">
                </div>
                
            </div>
            <div class="form-row float-right">
                
                <button type="submit " class=" float-right btn btn-primary">Submit</button>
            </div>

        </form>
         
            <div  class="col"><h1>{{ month }}</h1>
            <div  class=" daysView" >
                <div role="alert" v-for="(day,index) in datesOfCurrentMonth"  :key="index" :id="index+1" :value="day.day">
                        <div class="margin-bottom-5 alert alert-primary dates highlighted" v-show="day.isSelected">{{day.day}} - {{day.title}} </div>
                        <div class="margin-bottom-5 alert alert-primary dates" v-show="!day.isSelected">{{day.day}} </div>
                 </div>     
            </div>
            </div>
        

    </div>
            
</template>

<script>
    const Days = [{name:'Sunday'},{name:'Monday'},{name:'Tuesday'},{name:'Wednesday'},{name:'Thursday'},{name:'Friday'},{name:'Saturday'}];
    const today = new Date();
    
    
    
    const currentMonthDates = Array.from({length: moment().daysInMonth()}, (x, i) => moment().startOf('month').add(i, 'days').format("D dddd"));

    let datesOfCurrentMonth = currentMonthDates.map(date => ({
        day: date,
        isSelected: false,
        title: '',
    }));
    export default {
        data() {
            return {
                form: {
                    name: '',
                    dateFrom:'',
                    dateTo:'',
                    checkedDays:[],
                },
                month: new Date().toLocaleString('default', { month: 'long' }),
                Days,
                datesOfCurrentMonth,
                filteredDaysOfTheMonth:[],
                isColored:true,
               
            }
                
        },
        mounted() {
            
        },
        methods: {
            validateForm(){
                let formName = this.form.name;
                let dateFrom = this.form.dateFrom;
                let dateTo = this.form.dateTo;
                let checked = this.form.checkedDays;
                if (formName === ""){
                    alert("Title is blank");
                }
                else if ((dateFrom === "") || (dateTo === ""))  {
                    alert("Dates are blank");
                }
                else if (new Date(dateFrom) > new Date(dateTo)){
                    alert("From date is greater that To date");
                }
                else if (checked.length <= 0){
                    alert("Select day/s");
                }
                else{
                    let snackbar = document.getElementById("snackbar");
                    snackbar.className = "show";
                    snackbar.innerText = "Added Event: " + formName;
                    setTimeout(function(){ snackbar.className = snackbar.className.replace("show", ""); }, 3000);
              
                    this.submitForm();
                    
                }
                
            },
            getDatesBetweenDates(startDate, endDate) {
                let dates = []
                const theDate = new Date(startDate)
                while (theDate <= endDate) {
                    dates = [...dates, new Date(theDate)]
                    theDate.setDate(theDate.getDate() + 1)
                }
                return dates;
            },
            submitForm(){
                this.$forceUpdate();
                let checked = this.form.checkedDays;
                let data  = new FormData();
                data.append('name',this.form.name);
                data.append('start_time',this.form.dateFrom);
                data.append('end_time',this.form.dateTo);
                let filteredDates = [];
                let inBetweenDates = [];
                let daysOfTheMonth = [];

                let start = this.form.dateFrom;
                let end = this.form.dateTo;
                
                this.$nextTick(function () { 
                    inBetweenDates = this.getDatesBetweenDates(new Date(start), new Date(end));

                    filteredDates = inBetweenDates.filter(date => checked.includes(Days[date.getDay()].name));

                    daysOfTheMonth = filteredDates.map(date => date.getDate())
                    datesOfCurrentMonth.forEach((date, index) => {
                        
                        Vue.set(this.datesOfCurrentMonth, index, {...date, title: '', isSelected: false})
                        if (daysOfTheMonth.includes(index+1)) {
                            Vue.set(this.datesOfCurrentMonth, index, {...date, title: this.form.name, isSelected: true})
                        }
                    })

                });
                
              
                axios.post('./api/todo', data) 
                .then((res) => {
                    
                })
                .catch(error => {
                    console.log(error); 
                });
            }
        }
  }







</script>
