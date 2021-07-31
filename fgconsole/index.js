const tty = require('tty');
const FGConsole=require('./fgconsole');

const fgConsole=new FGConsole(process.stdout,()=>{
    process.exit();
},{
    language:'zh-cn'
});
process.stdin.on('data',(data)=>{
    fgConsole.ondata(data);
});
