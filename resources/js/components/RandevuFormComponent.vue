<template>
    <div>
        <div v-if="completeForm">
            <div class="container">
                <div class="main">
                    <h2>Randevu Takip Sistemi</h2>
                    <h4>Randevu oluşturmak için bilgileri doğru giriniz.</h4>

                </div>
                <div class="row">
                    <div class="col-md-12">
                        <ul>
                            <li class="errors" v-for="i in errors">
                                {{ i }}
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <input type="text" class="form-control" style="margin:5px;" v-model="name" placeholder="Ad Soyad">

                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <input type="text" class="form-control" style="margin:5px;" v-model="email" placeholder="Email">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <input type="text" class="form-control" style="margin:5px;" v-mask="'##-###-###-##-##'" v-model="phone" placeholder="Telefon">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <input  class="form-control" :min="minDate" @change="selectDate" style="margin:5px;" v-model="date" type="date">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <ul class="select-time-ul">
                            <li v-for="item in workingHours " class="select-time">
                                <input v-if="item.isActive" type="radio" v-model="workingHour" v-bind:value="item.id">

                                <span>{{item.hours}}</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <textarea v-model="text" id="" class="form-control" cols="30" rows="5"></textarea>

                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-md-12 notification-area">
                        <div class="form-group">
                            <input id="sms" type="radio" v-model="notification_type" value="0">
                            <label for="sms">Sms</label>
                        </div>
                        <div class="form-group">
                            <input id="email" type="radio" v-model="notification_type" value="1">
                            <label for="email">Email</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-center" >
                        <button v-on:click="store" class="btn btn-success"> Randevu Oluştur</button>
                    </div>
                </div>
            </div>
        </div>
        <div  v-if="!completeForm">
            <div class="complete-form">
                <img  src="https://sezgidisklinigi.com/wp-content/uploads/2019/10/emblemok_103757.png" width="50px;" height="50px;">
                <span style="color:green; font-size:20px;"> Randevunuz Başarıyla Alınmıştır</span>
            </div>
        </div>
    </div>
</template>
<script>

export default {

    data(){
        return {
            completeForm:true,
            errors:[],
            notification_type:null,
            workingHour:0,
            name:null,
            email:null,
            phone:null,
            text:null,
            minDate:new Date().toISOString().slice(0,10),
            date:new Date().toISOString().slice(0,10),
            workingHours:[]
        }
    },

    mounted(){
        axios.get(`http://127.0.0.1:8000/api/working-hours`)
            .then((res)=>{
                this.workingHours = res.data;
            })
    },

    methods:{
        store:function () {
            if(this.notification_type!=null && this.name!=null && this.email!=null && this.validEmail(this.email) && this.phone!=null && this.workingHour!=0)
            {
                axios.post(`http://127.0.0.1:8000/api/appointment-store`,{
                    fullName:this.name,
                    phone:this.phone,
                    email:this.email,
                    date:this.date,
                    workingHour:this.workingHour,
                    notification_type:this.notification_type
                }).then((res)=>{
                    if(res.status)
                    {

                        this.completeForm = false;
                    }
                })
            }
            this.errors = [];
            if(!this.notification_type)
            {
                this.errors.push('Bildirim Tipi Seçilmelidir');
            }
            if(!this.name) {
                this.errors.push('İsim Soyisim Girilmelidir');
            }
            if(!this.email || !this.validEmail(this.email)) {
                this.errors.push('Email Girilmelidir ve Formatı Doğru olmalıdır');
            }
            if(!this.phone) {
                this.errors.push('Telefon numarası Girilmelidir');
            }
            if(!this.workingHour) {
                this.errors.push('Çalışma saati seçilmelidir');
            }
        },
        selectDate:function(){
            axios.get(`http://127.0.0.1:8000/api/working-hours/${this.date}`)
                .then((res)=>{
                    this.workingHours = res.data;
                })
        },
        validEmail: function (email) {
            var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(email);
        }
    }
}
</script>
