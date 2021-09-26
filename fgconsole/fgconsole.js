const fecha=require('fecha');
const axios=require('axios');
const i18next=require('i18next');
const Terminal=require('./util').Terminal;
const LANGUAGE_PACK_DATA={
    'zh.CN':{
        translation:{
            'Flight Control Center':'飞控中心',
            'Loading':'加载中……',
            KEYBOARD_HELP_IDLE:'Esc：退出',
            KEYBOARD_HELP:'Esc：退出    s：开启/关闭声音    p：暂停/继续',
            'No Flight Mission':'没有飞行任务。',
            'Aircraft Model':'机种',
            'UTC Time':'UTC时间',
            'Local Time':'本地时间',
            'Longitude':'经度',
            'Latitude':'纬度',
            'Flight Time':'飞行时间',
            'Remaining Time':'剩余时间',
            'Total Distance':'总里程',
            'Remaining Distance':'剩余里程',
            'Elapsed Distance':'飞行里程',
            'In Flight':'飞行中',
            'Paused':'已暂停',
            'Crashed':'已坠毁',
            'Direction':'方向',
            'AGL':'相对高度',
            'Altitude':'海拔高度',
            'Vertical Speed':'垂直速度',
            'Speed':'速度',
            'Airspeed':'空速',
            'Groundspeed':'地速',
            'Mach':'马赫数',
            'Fuel':'燃料'
        }
    },
    'ja':{
        translation:{
            'Flight Control Center':'飛行制御センター',
            'Loading':'Loading...',
            KEYBOARD_HELP_IDLE:'Esc：終了',
            KEYBOARD_HELP:'Esc：終了    s：ｻｳﾝﾄﾞｵﾝ/ｵﾌ    p：一時停止/再開',
            'No Flight Mission':'飛行任務がありません。',
            'Aircraft Model':'機種',
            'UTC Time':'UTC時間',
            'Local Time':'地方時間',
            'Longitude':'経度',
            'Latitude':'緯度',
            'Flight Time':'飛行時間',
            'Remaining Time':'残り時間',
            'Total Distance':'総距離',
            'Remaining Distance':'残り距離',
            'Elapsed Distance':'飛行距離',
            'In Flight':'飛行中',
            'Paused':'一時停止',
            'Crashed':'墜落しました',
            'Direction':'方向',
            'AGL':'対地高度',
            'Altitude':'海抜高度',
            'Vertical Speed':'垂直速度',
            'Speed':'速度',
            'Airspeed':'対気速度',
            'Groundspeed':'対地速度',
            'Mach':'マッハ数',
            'Fuel':'燃料'
        }
    }
};

module.exports=class extends Terminal{
    data=undefined;
    running=true;
    flightStatus=null;
    constructor(stream,_exit,param={}){
        super(stream,{
            outputEncoding:param.encoding
        });
        this._exit=_exit;
        this._timer=null;
        
        i18next.init({
            lng:param.language,
            resources:LANGUAGE_PACK_DATA
        }).then((t)=>{
            this.$t=t;
            this.FGFS_HOST=param.FGFS_HOST;
            this.clrscr();
            this.locate(0,0);
            this.setcursor(false);
            this.print(this.$t('Loading'));
            this.refresh();
        });
    }
    drawFrame(){
        this.clrscr();
        this.locate(0,1);
        this.setattr(Terminal.ATTR_BOLD,Terminal.ATTR_REVERSED,Terminal.FG_YELLOW,Terminal.BG_CYAN);
        this.print(' '.repeat(79));
        let title=this.$t('Flight Control Center');
        this.locate(Math.round((80-Terminal.strlen(title))/2),1);
        this.print(title);
        
        this.locate(0,24);
        this.setattr(Terminal.RESET_ATTR,Terminal.ATTR_REVERSED,Terminal.FG_BLACK,Terminal.BG_CYAN);
        this.print(' '.repeat(79));
        this.locate(2,24);
        this.print(this.$t(this.data ? 'KEYBOARD_HELP' : 'KEYBOARD_HELP_IDLE'),79);
        this.setattr(Terminal.RESET_ATTR);
    }
    _drawTime(){
        let timestr=fecha.format(new Date(),'YYYY-MM-DD HH:mm:ss');
        this.setattr(Terminal.ATTR_REVERSED,Terminal.FG_BLACK,Terminal.BG_CYAN);
        this.locate(80-timestr.length,1);
        this.print(timestr);
        this.setattr(Terminal.RESET_ATTR);
    }
    async refresh(){
        if(!this.running){
            return;
        }
        this._timer=setTimeout(()=>{
            if(this.running){
                this.refresh();
            }
        },1000);

        let data=null;
        try{
            data=await axios({
                method:'GET',
                url:this.FGFS_HOST+'/json/fgreport'
            });
            if(!this.running){
                return;
            }
        }catch(e){
            if(!this.running){
                return;
            }
            if(null!==this.data){
                this.data=null;
                this.drawFrame();
                let text=this.$t('No Flight Mission');
                this.locate(Math.floor((80-Terminal.strlen(text))/2),12);
                this.print(text);
            }
            this._drawTime();
            return;
        }

        try{
            let fgreport={};
            for(let item of data.data.children){
                fgreport[item.name]=item.value;
            }

            this.setattr(Terminal.RESET_ATTR);

            //绘制框架
            if(!this.data){
                this.data=fgreport;
                this.drawFrame();

                this.locate(0,3);
                this.print(this.$t('Aircraft Model'));
                this.locate(0,4);
                this.print(this.$t('UTC Time'));
                this.locate(0,5);
                this.print(this.$t('Local Time'));
                this.locate(0,6);
                this.print(this.$t('Longitude'));
                this.locate(0,7);
                this.print(this.$t('Latitude'));
                this.locate(0,8);
                this.print(this.$t('Flight Time'));
                this.locate(0,9);
                this.print(this.$t('Remaining Time'));
                this.locate(0,10);
                this.print(this.$t('Total Distance'));
                this.locate(0,11);
                this.print(this.$t('Remaining Distance'));
                this.locate(0,12);
                this.print(this.$t('Elapsed Distance'));

                this.locate(40,4);
                this.print(this.$t('Direction'));
                this.locate(40,5);
                this.print(this.$t('Altitude'));
                this.locate(40,6);
                this.print(this.$t('AGL'));
                this.locate(40,7);
                this.print(this.$t('Vertical Speed'));
                if('ufo'==fgreport['flight-model']){
                    this.locate(40,8);
                    this.print(this.$t('Speed'));
                }else{
                    this.locate(40,8);
                    this.print(this.$t('Airspeed'));
                    this.locate(40,9);
                    this.print(this.$t('Groundspeed'));
                    this.locate(40,10);
                    this.print(this.$t('Mach'));
                    this.locate(40,11);
                    this.print(this.$t('Fuel'));
                }
            }
            fgreport['longitude'] = Math.abs(fgreport['longitude-deg']).toFixed(6)+(fgreport['longitude-deg']>=0 ? 'E' : 'W');
            fgreport['latitude'] = Math.abs(fgreport['latitude-deg']).toFixed(6)+(fgreport['latitude-deg']>=0 ? 'N' : 'S');
            let padSize=11;
            this.locate(padSize,3);
            this.print(fgreport.aircraft+'        ');
            this.locate(padSize,4);
            this.print(fgreport['gmt-string']+'        ');
            this.locate(padSize,5);
            this.print(fgreport['local-time-string']+'        ');
            this.locate(padSize,6);
            this.print(fgreport['longitude']+'        ');
            this.locate(padSize,7);
            this.print(fgreport['latitude']+'        ');
            this.locate(padSize,8);
            this.print(fgreport['flight-time-string']+'        ');
            this.locate(padSize,9);
            this.print(fgreport['ete-string']+'        ');
            this.locate(padSize,10);
            this.print(fgreport['total-distance'].toFixed(1)+'nm        ');
            this.locate(padSize,11);
            this.print(fgreport['distance-remaining-nm'].toFixed(1)+'nm        ');
            this.locate(padSize,12);
            this.print((Number(fgreport['total-distance'])-Number(fgreport['distance-remaining-nm'])).toFixed(1)+'nm        ');
            
            this.locate(40+padSize,4);
            this.print((Number(fgreport['heading-deg'])).toFixed(2)+'°        ');
            this.locate(40+padSize,5);
            this.print((Number(fgreport['altitude-ft'])).toFixed(1)+'ft        ');
            this.locate(40+padSize,6);
            this.print((Number(fgreport['altitude-agl-ft'])).toFixed(1)+'ft        ');
            this.locate(40+padSize,7);
            this.print((Number(fgreport['vertical-speed-fps'])*60).toFixed(1)+'ft/min        ');
            if('ufo'==fgreport['flight-model']){
                this.locate(40+padSize,8);
                this.print((Number(fgreport['vertical-speed-fps'])).toFixed(1)+'kts        ');
            }else{
                this.locate(40+padSize,8);
                this.print((Number(fgreport['airspeed-kt'])).toFixed(1)+'kts        ');
                this.locate(40+padSize,9);
                this.print((Number(fgreport['groundspeed-kt'])).toFixed(1)+'kts        ');
                this.locate(40+padSize,10);
                this.print((Number(fgreport['mach'])).toFixed(4)+'     ');
                this.locate(40+padSize,11);
                let fuelPercentage=Number(fgreport['remain-fuel'])/Number(fgreport['initial-fuel'])*100;
                this.print(fuelPercentage.toFixed(2)+'%     ');
            }

            let status=null;
            if(fgreport['crashed']){
                status='crashed';
            }else if(fgreport['paused']){
                status='paused';
            }else{
                status='running';
            }
            if(this.flightStatus!=status){
                this.flightStatus=status;
                this.locate(0,23);
                this.clrline();
                switch(status){
                    case 'crashed':
                        this.setattr(Terminal.ATTR_BOLD,Terminal.ATTR_UNDERLINE,Terminal.FG_YELLOW,Terminal.BG_RED);
                        this.print(this.$t('Crashed'));
                    break;
                    case 'paused':
                        this.setattr(Terminal.ATTR_BOLD,Terminal.FG_YELLOW);
                        this.print(this.$t('Paused'));
                    break;
                    default:
                        this.setattr(Terminal.ATTR_BOLD,Terminal.FG_GREEN);
                        this.print(this.$t('In Flight'));
                    break;
                }
                this.setattr(Terminal.RESET_ATTR);
                this.print(' '.repeat(20));
            }
            this._drawTime();
        }catch(e){
            console.error(e);
        }
    }
    destroy(){
        try{
            this.running=false;
            if(this._timer){
              clearTimeout(this._timer);
            }
            this.reset();
        }catch(e){
            console.error(e);
        }finally{
            this._exit();
        }
    }
    async toggleSound(){
        if(null===this.data){
            return;
        }
        let {data}=await axios({
            method:'GET',
            url:this.FGFS_HOST+'/json/sim/sound/enabled'
        });
        await axios({
            method:'GET',
            url:this.FGFS_HOST+`/props/sim/sound?submit=set&enabled=${data.value ? 'false' : 'true'}`
        });
    }
    async togglePause(){
        if(null===this.data){
            return;
        }
        await axios({
            method:'GET',
            url:this.FGFS_HOST+'/run.cgi?value=pause'
        });
    }
    ondata(data){
        try{
            switch(data[0]){
                case 0x1b:
                    this.destroy();
                    return;
                break;
                case 0x73:
                    this.toggleSound();
                break;
                case 0x70:
                    this.togglePause();
                break;
            }
        }catch(e){
            console.error(e);
        }
    }
}
