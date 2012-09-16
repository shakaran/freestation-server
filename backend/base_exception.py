#!/usr/bin/env python
# -*- coding: utf-8 -*-

class FSBaseException(Exception):
    def __init__(self, message = None):
        '''
            Undocumented
        '''
        self.message = message
    
    def __str__(self):
        '''
            Undocumented
        '''
        return repr(self.__class__.__name__  + ': ' + self.message)
        
    def __getitem__(self):
        '''
            Undocumented
        '''
        pass

    def __doc__(self): #@ReservedAssignment
        '''
            FSBaseException class.
        '''
        pass
    
    def __len__(self):
        '''
            Undocumented
        '''
        pass