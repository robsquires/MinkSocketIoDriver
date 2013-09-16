Feature: A Mink driver can open the socketIO client


Scenario: A Mink driver can open the socketIO client
    Given the client is connected to the server "127.0.0.1"
      And the client is subscribed to the event "new_msg"
     When the server emits the event "new_msg" with the payload:
     Then the client should receive from the event "new_msg" the following payload:




Scenario: The client can be instructed to connect to a particular server

Scenario: The client can be instructed to listen to a particular websocket event

Scenario: Behat can request the websocket packet received by the client
