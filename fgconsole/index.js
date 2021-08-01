const tty = require('tty');
const FGConsole=require('./fgconsole');

const fgConsole=new FGConsole(process.stdout,()=>{
  process.stdin.setRawMode(false);
  process.stdin.resume();
  process.exit();
},{
  FGFS_HOST:"http://10.0.2.2:8123",
  language:'zh.CN'
});
process.stdin.on('data',(data)=>{
  fgConsole.ondata(data);
});
process.stdin.setRawMode(true);
process.stdin.resume();

