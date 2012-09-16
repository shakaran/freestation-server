FreeStation Server
==============================================

.. figure::  ../img/fs-logo.png
   :align:   center

Note: This documentation lives at `http://freestation.quijost.com/docs/>`_.  If you're reading it somewhere else, you may
not have the latest version.

This documentation is provided by the author "as is" without any express or
implied warranties.  See :ref:`the documentation license <license>` for more details.

FreeStation Server is the backend python based part of :program:`FreeStation` |version|..


It can be run with::

    $ fs_server.py 

Features:
   * Backend2 based on python
   * Connection with ZeroC ICE through python2.

.. sphinx-build -b html . _build
.. sphinx-apidoc -F -o docs .
 
.. .. warning::

   Never, ever, use this code!

.. Here is something I want to talk about::

    def my_fn(foo, bar=True):
        """A really useful function.

        Returns None
        """
 
Contents:

.. toctree::
   :numbered:
   :maxdepth: 2

   intro
   tutorial
   installation
   quickstart
   faq
   license
   changelog
   api
   fs_server
   fs_ice
   backend
   backend.FS
   base_exception
   FS
   
.. .. automodule:: backend.fs_server
   :members:
   :show-inheritance:
   :undoc-members:
   
.. .. autoclass:: FreeStationServer 
   :members:
   
Indices and tables
==================

* :ref:`genindex`
* :ref:`modindex`
* :ref:`search`