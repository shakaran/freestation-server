// #include <Process.ice>
//[["python:package:zeroc"]]
[["cpp:include:list"]]
module FS {
   sequence<byte> File;
   sequence<byte> FileBlock;
   
   interface Api {
     void getWidgets();
     string getXMLWidgets();
     void version(out string sout);
     int getFileSize(string path);
     File getFile(string path);
     FileBlock getFileChunk(string path, int pos, int size);
     void isAuthorized(out bool sout);
     int getClientId();
   };
   interface Widget 
   {
     void getName();
   };
   
   exception GenericError {
     string reason;
   };
   exception NoAuthorized extends GenericError {};
};