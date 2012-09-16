#!/usr/bin/env python
# -*- coding: utf-8; tab-width: 4; mode: python -*-
# emacs: -*- mode: python; py-indent-offset: 4; indent-tabs-mode: t -*-
# vi: set ft=python sts=4 ts=4 sw=4 noet 

import os
import FS

from api_manager import ApiManager

import Ice

from base_exception import FSBaseException

class NoAuthorized(FSBaseException):
    def __init__(self, message = None):
        self.message = message
        FSBaseException.__init__(self, message)
        
class ClientStatusDisabled(FSBaseException):
    def __init__(self, message = None):
        self.message = message
        FSBaseException.__init__(self, message)
        
class Api(FS.Api): #@UndefinedVariable
    def __init__(self, server):
        '''
            Init the Api with Ice objects created.
        '''
        FS.Api.__init__(self)
        self.server = server
        self.manager = ApiManager(server)
    
    def get_widgets(self):
        pass
    
    def getXMLWidgets(self, current = None):
        self.manager.set_current(current)
        self.manager.check_authorized()
        self.manager.update_client_requests()
        
        return self.manager.generate_xml_widgets()
        
    
    def version(self, current = None):
        self.manager.set_current(current)
        self.manager.check_authorized()
        self.manager.update_client_requests()
        
        self.server.current_logger._print('Request hello')
        self.server.current_logger.warning('Request hello')
        self.server.current_logger.error('Request hello')
        self.server.current_logger.trace('cat', 'Request hello')
        self.server.current_logger._print('Context counter:' + self.server.context.get('counter'))
        self.server.current_logger._print('¡hello world!')
        
        # http://doc.zeroc.com/display/Ice/Ice-Current
        self.server.current_logger._print('Scan ' +  str(current)) 

        # http://doc.zeroc.com/display/Ice/Ice-Current#Ice-Current-adapter
        print 'Adapter', current.adapter.getName() # ::Ice::ObjectAdapter
        #print dir(current.adapter)
        print current.adapter.getCommunicator().getImplicitContext() # Ice::ImplicitContext
        print current.adapter.getCommunicator().getDefaultRouter() # Ice::Router
        # Ice::Locator* getDefaultLocator()
        # Ice::PluginManager getPluginManager()
        current.adapter.getCommunicator().flushBatchRequests()
        
        # Ice::Context http://doc.zeroc.com/display/Ice/Ice-Current#Ice-Current-ctx
        print 'Context:', current.ctx #http://doc.zeroc.com/display/Ice/Ice+Slice+API#IceSliceAPI-Context
        print 'Context:', current.ctx #http://doc.zeroc.com/display/Ice/Ice-ImplicitContext#Ice-ImplicitContext-getContext
        print 'Facet:', current.facet
        
        # Ice::Identity http://doc.zeroc.com/display/Ice/Ice-Current#Ice-Current-id
        # http://doc.zeroc.com/display/Ice/Ice-Identity
        print 'Id name:', current.id.name
        print 'Id category:', current.id.category
        # http://doc.zeroc.com/display/Ice/Ice-Current#Ice-Current-mode
        print 'Mode:', current.mode # 
        # Ice::OperationMode http://doc.zeroc.com/display/Ice/Ice-OperationMode
        print 'Operation:', current.operation # scan # Ice.OperationMode.Normal
        # http://doc.zeroc.com/display/Ice/Ice-Current#Ice-Current-requestId
        print 'Request id:', current.requestId # oneway (0) or collocated (-1).
        
        # http://doc.zeroc.com/display/Ice/Ice-Connection
        # http://doc.zeroc.com/display/Ice/Ice-Current#Ice-Current-con
        connection = current.con # IcePy.Connection
        connection.flushBatchRequests()
        # local address = 79.143.179.118:10000
        # remote address = 83.43.218.193:36806
        #print 'String:', connection.toString()
        print 'End point:', connection.getEndpoint() 
        print 'Timeout:', connection.timeout()
        print 'Type:', connection.type() # tcp or udp
        
        info = connection.getInfo() # IcePy.TCPConnectionInfo
        self.server.current_logger._print('Adapter name: ' + str(info.adapterName))
        self.server.current_logger._print('Incoming: ' + str(info.incoming))
        self.server.current_logger._print('Local address: ' + str(info.localAddress))
        self.server.current_logger._print('Local port: ' + str(info.localPort))
        self.server.current_logger._print('Remote address: ' + str(info.remoteAddress))
        self.server.current_logger._print('Remote port: ' + str(info.remotePort))
        

        force = False
        connection.close(force)
        

        version = 'FS Server '  + self.server.VERSION
        print current
        print version
        return version
    
    def getFile(self, current = None, path = None):
        self.manager.set_current(current)
        self.manager.check_authorized()
        self.manager.update_client_requests()
        
        f = open('../test.tar.gz', "r")
        data = f.read()
        f.close()
        
        return data
    
    def getFileChunk(self, path = None, pos = 0, size = 0, current = None):
        # Authentication could be enabled on each request, but slow down transfer
        #self.manager.set_current(current)
        #self.manager.check_authorized()
        #self.manager.update_client_requests()
        
        f = open('../' + str(path), 'r') # Put absolute path on /
        f.seek(int(pos), 0) # Seek absolute position
        data = f.read(int(size))
        f.close()
        
        return data    
    
    def getFileSize(self, path = None, current = None):
        self.manager.set_current(current)
        self.manager.check_authorized()
        self.manager.update_client_requests()
        
        file_size = os.path.getsize('../' + str(path))  # Put absolute path on /

        return file_size
    
    def isAuthorized(self, current = None):
        self.manager.set_current(current)
        
        result = self.manager.is_authorized()
        
        #self.server.current_logger._print('Result isAuthorized: ' + str(result))
        
        return result
    
    def getClientId(self, current = None):
        self.manager.set_current(current)
        self.manager.check_authorized()
        self.manager.update_client_requests()
        
        return self.manager.get_client_id()
        