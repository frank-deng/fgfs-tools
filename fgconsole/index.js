const tty = require('tty');
const FGConsole=require('./fgconsole');

const fgConsole=new FGConsole(process.stdout,()=>{
  process.stdin.setRawMode(false);
  process.stdin.resume();
  process.exit();
},{
  language:'zh-cn'
});
process.stdin.on('data',(data)=>{
  fgConsole.ondata(data);
});
process.stdin.setRawMode(true);
process.stdin.resume();

