#!/usr/bin/env python
# -*- coding: utf-8; tab-width: 4; mode: python -*-
# emacs: -*- mode: python; py-indent-offset: 4; indent-tabs-mode: t -*-
# vi: set ft=python sts=4 ts=4 sw=4 noet 

import time
import MySQLdb
from db_config import DBConfig

from base_exception import FSBaseException

class NoAuthorized(FSBaseException):
    def __init__(self, message = None):
        self.message = message
        FSBaseException.__init__(self, message)
        
class ClientStatusDisabled(FSBaseException):
    def __init__(self, message = None):
        self.message = message
        FSBaseException.__init__(self, message)
        
class ApiManager:
    def __init__(self, server):
        self.current = None
        self.server = server
    
        self.db = MySQLdb.connect(
                             host   = DBConfig.MYSQL_SERVER, 
                             user   = DBConfig.MYSQL_USER,
                             passwd = DBConfig.MYSQL_PASS,
                             db     = DBConfig.DEFAULT_DATABASE,
                             )
        
        #result = db.store_result()
        self.cursor = self.db.cursor()
        
    def set_current(self, current):
        self.current = current
    
    def get_client_ip(self):
        connection = self.current.con
        connection.flushBatchRequests()
        info = connection.getInfo()
        return info.remoteAddress
    
    def update_client_last_connection(self, ip):
        if not ip:
           ip = self.get_client_ip()
        
        
        self.cursor.execute("UPDATE clients SET last_connection='{0}' WHERE ip='{1}'".format(int(time.time()), ip))
        
    def update_client_requests(self, ip = None):
        if not ip:
           ip = self.get_client_ip()
        
        requests = self.get_client_request(ip)
        
        # self.server.current_logger._print("UPDATE clients SET requests=requests+1 WHERE ip='{1}'".format(requests + 1, ip))  )   
        self.cursor.execute("UPDATE clients SET requests='{0}' WHERE ip='{1}'".format(requests + 1, ip))
        self.db.commit() # Needs autocommit for InnoDB
            
    def get_client_id(self, ip = None):
        if not ip:
           ip = self.get_client_ip()
           
        try:
            self.cursor.execute("SELECT id FROM clients WHERE ip='{0}';".format(ip))
        except (AttributeError, MySQLdb.OperationalError), e: # (2006, 'MySQL server has gone away')
            self.server.current_logger.error('Server error: {0}'.format(e))
            return -1   
        
        rows = self.cursor.fetchall()
        if not rows:
            client_id = -1
        else:
            for record in rows:
                client_id = int(record[0])
        
        return client_id
    
    def get_client_request(self, ip = None):
        if not ip:
           ip = self.get_client_ip()
           
        try:
            self.cursor.execute("SELECT requests FROM clients WHERE ip='{0}';".format(ip))
        except (AttributeError, MySQLdb.OperationalError), e: # (2006, 'MySQL server has gone away')
            self.server.current_logger.error('Server error: {0}'.format(e))
            return -1   
        
        rows = self.cursor.fetchall()
        if not rows:
            requests = 0
        else:
            for record in rows:
                requests = int(record[0])
        
        return requests
        
    def check_authorized(self, ip = None):
        
        if not ip:
           ip = self.get_client_ip()

        try:
            self.cursor.execute("SELECT status FROM clients WHERE ip='{0}';".format(ip))
        except (AttributeError, MySQLdb.OperationalError), e: # (2006, 'MySQL server has gone away')
            self.server.current_logger.error('Server error: {0}'.format(e))
            return -1
        
        rows = self.cursor.fetchall()
        if not rows:
            self.server.current_logger.warning('Connection from IP {0} not authorized'.format(ip))
            raise NoAuthorized('IP {0} is not authorized'.format(ip))
        else:
            for record in rows:
                if record[0] != 1: # Check status enabled
                    self.server.current_logger._print('Status: ' + str(record[0]))
                    raise ClientStatusDisabled('IP {0} is an authorized client but disabled'.format(ip))
                    
                self.server.current_logger._print('Address {0} authorized'.format(ip))
        
        self.update_client_last_connection(ip)

        #self.cursor.close()
        #83.43.221.113
        
    def is_authorized(self):
        try:
            self.check_authorized()
            return True
        except NoAuthorized, e:
            return False
        except ClientStatusDisabled, e:
            return False
    
    def get_widgets_associated(self, client_id = None):
        ''' 
            Get the list of widgets availables for a given client.
        '''
        self.server.current_logger.warning("SELECT client_id, widget_id, widget_data FROM client_widgets WHERE client_id='{0}';".format(client_id))
        self.cursor.execute("SELECT client_id, widget_id, widget_data FROM client_widgets WHERE client_id='{0}';".format(client_id))
        
        widget_list = ()
        rows = self.cursor.fetchall()
        if not rows:
            widget_list = ()
        else:
            widget_list = rows
        
        return widget_list
    
    def get_widget_name(self, widget_id = None):
        ''' 
            Get the widget name for a given widget identifier.
        '''
        try:
            self.cursor.execute("SELECT name FROM widgets WHERE id='{0}';".format(widget_id))
        except (AttributeError, MySQLdb.OperationalError), e: # (2006, 'MySQL server has gone away')
            self.server.current_logger.error('Server error: {0}'.format(e))
            return -1   
        
        rows = self.cursor.fetchall()
        if not rows:
            widget_name = None
        else:
            for record in rows:
                widget_name = str(record[0])
        
        return widget_name
        
    def generate_xml_widgets(self, ip = None):
        
        if not ip:
           ip = self.get_client_ip()
        
        client_id = self.get_client_id(ip)
        widget_list = self.get_widgets_associated(client_id)
        # Search on client_widgets table for each widget_id on this client_id
        
        # Fetch all the data for each widget_id
        
        # Parse the names underscores to CamelCase and put on <name>
        # If data, put on <data> or autogenerate the <width> and the others
        
        data = '<?xml version="1.0" encoding="UTF-8"?>\n'
        data += '<interface>\n'
        
        if widget_list:
            for (client_id, widget_id, widget_data) in widget_list:
                widget_name = self.get_widget_name(widget_id)
                
                widget_name_aux = ''
                i = 0
                upper_flag = False
                for letter in widget_name:

                    if i == 0 or upper_flag:
                        letter = letter.upper()
                        upper_flag = False

                    elif letter == '_':
                        upper_flag = True
                        letter = ''
                    
                    widget_name_aux += letter
                    i += 1

                data += '\t<widget>\n'
                data += '\t\t<name>' + str(widget_name_aux)  + '</name>\n' # @todo Put on CamelCase name
                data += '\t\t<properties>\n'
                
                if widget_data:
                    # pip install phpserialize
                    from phpserialize import *
                    wdata = loads(widget_data)
                    properties = wdata['properties']
                    
                    if properties.has_key('homogeneous'):
                        data += '\t\t\t<homogeneous>' + str(properties['homogeneous']) + '</homogeneous>\n'
                        
                    if properties.has_key('spacing'):
                        data += '\t\t\t<spacing>' + str(properties['spacing']) + '</spacing>\n'
                        
                    if properties.has_key('width'):
                        data += '\t\t\t<width>' + str(properties['width']) + '</width>\n'
                        
                    if properties.has_key('height'):
                        data += '\t\t\t<height>' + str(properties['height']) + '</height>\n'
                        
                    if properties.has_key('height'):
                        data += '\t\t\t<data>' + str(properties['data']) + '</data>\n'   

                else:
                    #data += '\t\t\t' + str(widget_data) + '\n'
                    data += '\t\t\t<position type="relative" child="0" pack="None|Start|End">MainWindow</position>\n'
                    data += '\t\t\t<homogeneous>' + str(1) + '</homogeneous>\n'
                    data += '\t\t\t<spacing>' + str(5) + '</spacing>\n'
                    data += '\t\t\t<width>' + str(5) + '</width>\n'
                    data += '\t\t\t<height>' + str(5) + '</height>\n'
                    data += '\t\t\t<data>' + str('UCLM - FreeStation') + '</data>\n'
                    
                data += '\t\t</properties>\n'
                data += '\t</widget>\n'
                
        data += '</interface>\n'
        
        return data

