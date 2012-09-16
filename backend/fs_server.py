#!/usr/bin/env python
# -*- coding: utf-8; tab-width: 4; mode: python -*-
# emacs: -*- mode: python; py-indent-offset: 4; indent-tabs-mode: t -*-
# vi: set ft=python sts=4 ts=4 sw=4 noet 

import os, os.path
import sys
sys.path.append('/opt/Ice-3.4.2/python')
import sys
sys.stderr = open('ice_error.log', 'w') # Save errors on file (empty when ice error is used)
#sys.stdout = open('ice_output.log', 'w') # Save output on file (is overwrite for ICE, use running.log instead)
import time
import traceback
import Ice
import signal

# ln -s /opt/Ice-3.4.2/bin/slice2py /usr/bin/slice2py
# chmod +x FreeStationServer.py
# yum install screen
class States:

    # Node enabled but not in use
    STANDBY = 0

    # Node enabled and in use
    CONNECTED = 1

    # Node enabled but blocked
    BLOCKED = 2

    # Node enabled but not configured
    PARK = 3
 
import threading
import _threading_local
threading.local = _threading_local.local

from pprint import pprint, pformat

class ThreadLogger(Ice.ThreadNotification):
    def __init__(self):
        super(ThreadLogger, self).__init__()
        self._tls = threading.local()

    def start(self):
        self._tls.my_test_var = "_threading_local.local works"
        print "ice thread started: " + repr(threading.current_thread()) + " thread locals: " + pformat(self._tls.__dict__)

    def stop(self):
        print "ice thread stopped: " + repr(threading.current_thread()) + " thread locals: " + pformat(self._tls.__dict__) 
 
class FreeStationServer(Ice.Application):

    VERSION           = '1.0'
    ADAPTER_NAME      = 'ApiAdapter'
    COMUNNICATOR_NAME = 'Api' # A communicator contains one or more object adapters
    PORT = 10000
    MODE = 'udp' # connection mode: tcp or udp or default
    TIMEOUT = 60000 # 60 secs 
    DEBUG = False
    
    def __init__(self):
        '''
            Start a new instance of Ice.Application with signal handle (default)
        '''
        Ice.Application.__init__(self, Ice.Application.HandleSignals)
        print 'FreeStationServer', self.VERSION
        print 'PID: ' + str(os.getpid())
        signal.signal(signal.SIGTERM, self.destroy)
         
        self.adapter = None
         
        ice_version = Ice.stringVersion()
        print 'Ice Version', ice_version
        print 'Ice Version (integer)', Ice.intVersion()
        print 'Module source', Ice.__file__
        print 'Slice dir:', Ice.getSliceDir()
        
        self.current_logger = Ice.getProcessLogger() # ::Ice::Logger
        Ice.setProcessLogger(self.current_logger)
        
    def __str__(self):
        print 'FreeStationServer', self.VERSION
        
    def __call__(self):
        pass
        
    def init_properties(self):
        print 'Loading properties'
        
        self.config_file = None

        self.data = Ice.InitializationData()
        self.data.threadHook = ThreadLogger()
        self.data.properties = Ice.createProperties(None, self.data.properties)
        self.data.properties.setProperty('Ice.Config', '0')
        self.data.properties.setProperty('Ice.ProgramName', 'FreeStationServer')
        self.data.properties.setProperty('Ice.ImplicitContext', 'Shared')
        # Ice::MemoryLimitException 
        self.data.properties.setProperty('Ice.MessageSizeMax', '1024000') # 10240 KB = 10 MB (default 1 MB) 
        
        #self.data.properties.setProperty('Ice.ThreadPool.Server.Size', '1')
        #self.data.properties.setProperty('Ice.ThreadPool.Server.SizeMax', '1')
        
        
        if(self.DEBUG):
            self.data.properties.setProperty('Ice.PrintProcessId', '1')
            
        self.data.properties.setProperty('Ice.StdErr', 'ice_error.log')
        self.data.properties.setProperty('Ice.StdOut', 'ice_output.log')
        self.data.properties.setProperty('Ice.PrintAdapterReady', '1')
        self.data.properties.setProperty('Ice.PrintStackTraces', '1')
        self.data.properties.setProperty('Ice.UseSyslog', '0')
        self.data.properties.setProperty('Ice.NullHandleAbort', '0')
        self.data.properties.setProperty('Ice.Nohup', '0')
        
        if(self.DEBUG):
            # http://doc.zeroc.com/display/Ice/Ice+Trace+Properties
            self.data.properties.setProperty('Ice.Trace.GC', '2')
            self.data.properties.setProperty('Ice.Trace.Locator', '2')
            self.data.properties.setProperty('Ice.Trace.ThreadPool', '1')
            self.data.properties.setProperty('Ice.Trace.Network', '2')
            self.data.properties.setProperty('Ice.Trace.Protocol', '1')
            self.data.properties.setProperty('Ice.Trace.Retry', '2')
            self.data.properties.setProperty('Ice.Trace.Slicing', '1')
        
        # Warning properties http://www.zeroc.com/doc/Ice-3.2b/manual/PropRef.49.3.html
        # http://doc.zeroc.com/display/Ice/Ice+Warning+Properties
        self.data.properties.setProperty('Ice.Warn.AMICallback', '1')
        self.data.properties.setProperty('Ice.Warn.Connections', '1')
        self.data.properties.setProperty('Ice.Warn.Datagrams', '1')
        self.data.properties.setProperty('Ice.Warn.Dispatch', '2')
        self.data.properties.setProperty('Ice.Warn.Endpoints', '1')
        self.data.properties.setProperty('Ice.Warn.UnknownProperties', '1')
        
        if(self.DEBUG):
            self.data.properties.setProperty('Ice.Warn.UnusedProperties', '1')
        
        self.data.properties.setProperty('Ice.Admin.DelayCreation', '0')
        self.data.properties.setProperty('Ice.Admin.ServerId', '1')
        self.data.properties.setProperty('Ice.Admin.AdapterId', '1')
        self.data.properties.setProperty('Ice.Admin.Endpoints', '')
        
        #Ice.Admin.Endpoints=tcp -h 127.0.0.1
        """ 
        self.data.properties.setProperty('Ice.Admin.Locator', '1')
        self.data.properties.setProperty('Ice.Admin.PublishedEndpoints', '1')
        self.data.properties.setProperty('Ice.Admin.RegisterProcess', '1')
        self.data.properties.setProperty('Ice.Admin.ReplicaGroupId', '1')
        self.data.properties.setProperty('Ice.Admin.Router', '1')
        self.data.properties.setProperty('Ice.Admin.ProxyOptions', '1')
        self.data.properties.setProperty('Ice.Admin.ThreadPool.Size', '1')
        self.data.properties.setProperty('Ice.Admin.ThreadPool.SizeMax', '1')
        self.data.properties.setProperty('Ice.Admin.ThreadPool.SizeWarn', '1')
        self.data.properties.setProperty('Ice.Admin.ThreadPool.StackSize', '1')
        self.data.properties.setProperty('Ice.Admin.ThreadPool.Serialize', '1')
        self.data.properties.setProperty('Ice.Admin.DelayCreation', '1')
        self.data.properties.setProperty('Ice.Admin.Facets', '1')
        self.data.properties.setProperty('Ice.Admin.InstanceName', '1')
        self.data.properties.setProperty('Ice.Admin.ServerId', '1')
        
        self.data.properties.setProperty('Ice.IPv4', '1')
        self.data.properties.setProperty('Ice.IPv6', '1')
        
        self.data.properties.setProperty('Ice.EventLog\\.Source', '1')
        
        self.data.properties.setProperty('Ice.BackgroundLocatorCacheUpdates', '1')
        self.data.properties.setProperty('Ice.BatchAutoFlush', '1')
        self.data.properties.setProperty('Ice.ChangeUser', '1')
        """
        
        #data.properties.setProperty('Ice.Default.Protocol', 'udp')
        self.data.properties.setProperty('Ice.Default.Host', '') #localhost
        
        # Ice.Default.CollocationOptimized More efficient enabled, but don't supported on Python
        # http://www.zeroc.com/doc/Ice-3.2b/manual/PropRef.49.7.html
        # http://doc.zeroc.com/display/Ice/Ice+Default+and+Override+Properties#IceDefaultandOverrideProperties-Ice.Default.CollocationOptimized
        self.data.properties.setProperty('Ice.Default.CollocationOptimized', '0') # Deprecated
        self.data.properties.setProperty('Ice.Override.Timeout', str(self.TIMEOUT))
        self.data.properties.setProperty('Ice.Override.ConnectTimeout', str(self.TIMEOUT))
        # http://www.zeroc.com/doc/Ice-3.2b/manual/PropRef.49.7.html
        self.data.properties.setProperty('Ice.Override.Compress', '1') # http://doc.zeroc.com/display/Ice/Protocol+Compression 
        self.data.properties.setProperty('Ice.ACM.Server', '0')
        
        self.data.properties.setProperty('Ice.Compression.Level', '5') # From 1(min, fastest) to 9 (max, slow)
        self.data.properties.setProperty('Ice.CacheMessageBuffers', '1')
    
    def load_ice_interface(self):   
        '''
            Load the .ice files and generate FS folder with python slices.
        ''' 
        load_ice_path = 'ice/fs.ice'
        
        print 'Loading .ice interface from ' + load_ice_path
        
        if not os.path.exists('ice/fs.ice'):
           sys.stderr.write('Error: Could not find ice definition file: ice/fs.ice. Aborting.\n' % str(e))
           exit(-1)
        else:
            try:
                Ice.loadSlice(load_ice_path, ['-I' '/usr/share/slice'])
            except RuntimeError, e:
                sys.stderr.write('Error: Could not load ice definition file: %s\n' % str(e))
                exit(-1)
            except Exception, e:
                sys.stderr.write('Error: Could not load ice definition file: %s\n' % str(e))
                exit(-1)
            
            if not os.path.exists('FS') or not os.path.exists('fs_ice.py'):
                print 'Generating python slices'
                os.system('slice2py --underscore --output-dir . ice/fs.ice') 
    
    def show_communicator_properties(self):
        if self.DEBUG:
            print 'Communicator Properties:'
            print self.communicator().getProperties()
            print 'Command Line options:', self.communicator().getProperties().getCommandLineOptions()
        self.context = self.communicator().getImplicitContext() # Ice::ImplicitContext
        self.context.put('counter', '0')
        print self.communicator().getDefaultRouter() # Ice::Router
        
            #print 'Stats:', self.communicator().getStats() # RuntimeError: operation getStats not implemented
            
            # Get the Communicator Logger and It writes messages to the standard error output
            # http://doc.zeroc.com/display/Ice/The+Default+Logger
            # It could change to http://doc.zeroc.com/display/Ice/Built-in+Loggers
            #self.logger = self.communicator().getLogger()
            
            # http://doc.zeroc.com/display/Ice/Logger+Facility
            #self.logger._print('Print message')
            #self.logger.warning('Warning message')
            #self.logger.error('Error message')
            #self.logger.trace('Category', 'Trace message')
            
    
    def show_endpoints_info(self):
        print 'Endpoints:' # Adapter provides one or more transport endpoints
        for endpoint in self.adapter.getEndpoints():
            print endpoint
            
        print 'Published endpoints:'
        for published_endpoint in self.adapter.getPublishedEndpoints():
            print published_endpoint
            
        self.show_endpoints_info()
          
    def create_adapter(self):
        '''
            First step. Create object adapter, that maps Ice objects to servants
            Adapter also shares the thread pools of its communicator.
            It also could configure a individual with own thread pool (private thread pool)
        '''
        
        try:
            print 'Creating adapter object'
            # http://www.zeroc.com/doc/Ice-3.2b/manual/ProxyEndpointRef.50.2.html
            # This wrong, but create tcp and udp
            # TCP: tcp ‑h host ‑p port ‑t timeout ‑z
            # UDP: udp ‑v major.minor ‑e major.minor ‑h host ‑p port ‑z
            # SSL: ssl ‑h host ‑p port ‑t timeout ‑z
            self.adapter = self.communicator().createObjectAdapterWithEndpoints(self.ADAPTER_NAME, 'default -p ' + str(self.PORT) + ' -t ' + str(self.TIMEOUT) + ' -z:' + self.MODE)
        except Ice.SocketException, e: #@UndefinedVariable
            if e.error == 98:
                sys.stderr.write('Error: Address already in use. Please, check if the server is previously running.\nServer abort.\n')
            else:
                sys.stderr.write('Error %s: could not create the socket: %s\nServer abort.\n' % (str(e.error), str(e)))
            
            self.destroy()
                
            return -1
        except Ice.EndpointParseException, e: #@UndefinedVariable
            sys.stderr.write('Error: could not create end point: %s\nServer abort.\n' % str(e.str))
            
            self.destroy()
            return -1
        except Exception, e:
            sys.stderr.write('Error: unknown error: %s\nServer abort.\n' % str(e))
            self.destroy()
            return -1
        
        print 'Adapter Name:', self.adapter.getName()
        # http://doc.zeroc.com/display/Ice/Creating+an+Object+Adapter
        # http://doc.zeroc.com/display/Ice/Communicators
        print 'Adapter Comunicator:', self.adapter.getCommunicator() # ::Ice::Communicator
        
    def create_servant(self):
        '''
            Step 2. Create servant (ServantI instance)
            All servants for all Ice objects are permanently in memory.
            The servant would not store too much states for avoid out of memory and scale properly.
            Instead use servant locators for shrink memory.
            It is possible to register a single servant with multiple identities
        '''
        from api import Api
        print 'Importing servant Api'
        self.servant = Api(self) # ::FS::Api with all the methods (CamelCase and underscore?)
        
    def destroy(self):
        '''
            Clean and destroy the server
        '''
        
        print 'Destroying server.'
        return 0
        
    def start(self):
        return self.main(sys.argv, self.config_file, self.data)
        
    def run (self, argv):
        '''
            Start a server runnable instance with multi-thread
            http://doc.zeroc.com/display/Ice/The+Ice+Threading+Model
        '''
        
        # Ensure the program name
        self.communicator().getProperties().setProperty('Ice.ProgramName', 'FreeStationServer')
        self.shutdownOnInterrupt()
        
        print 'Admin:', self.communicator().getAdmin()
        
        self.show_communicator_properties()
        
        self.create_adapter()
        
        self.create_servant()
        
        # 3) Register servant in the adapter
        base_servant_adapter = self.adapter.add(self.servant, self.communicator().stringToIdentity(self.COMUNNICATOR_NAME))
        # Print remote object reference
        print 'Base servant adapter:', base_servant_adapter
        
        self.current_logger._print('Current logger print')
        
        # Make the servant as a particular Ice object known to the Ice run time. 
        # This adds a entry on Active Servant Map (ASM) and it waits until the first request is activated.
        print 'Enabling adapter object'
        self.adapter.activate() 
        
        print '[%s] Server started successfully. ' % time.time()

        # 4) Suspend the main thread and wait until the server ends
        self.communicator().waitForShutdown()
        
        if self.interrupted():
            self.current_logger._print(self.appName() + ': terminating')
            
        if self.destroyOnInterrupt():
            self.current_logger._print(self.appName() + ': destroying')
           
        if self.shutdownOnInterrupt():
            self.current_logger._print(self.appName() + ': shutdown')
            
        return self.destroy()

if __name__ == '__main__':
    fs_server = FreeStationServer()
    fs_server.init_properties()
    fs_server.load_ice_interface()
    
    sys.exit(fs_server.start())