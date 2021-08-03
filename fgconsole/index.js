const tty = require('tty');
const FGConsole=require('./fgconsole');

const fgConsole=new FGConsole(process.stdout,()=>{
  process.stdin.setRawMode(false);
  process.stdin.resume();
  setTimeout(process.exit,200);
},{
  FGFS_HOST:"http://10.0.2.2:8123",
  language:process.env.FGCONSOLE_LANGUAGE||'zh.CN',
  encoding:process.env.ENCODING
});
process.stdin.on('data',(data)=>{
  fgConsole.ondata(data);
});
process.stdin.setRawMode(true);
process.stdin.resume();

