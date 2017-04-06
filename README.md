# 2FA-Self-Enrollment-Site
Self enrollment website for Google Authentication 2FA system with F5 BIG-IP VPN

## Site Overview
1. a valid login is required via LDAP to enroll
2. users can either do a NEW enrollment or UPDATE an existing one
3. based on the enrollment type, the server connects to our VPN appliance and updates the user-token database
4. logs are generated on the server regarding who enrolled as well as the commands/results of configuration changes on the VPN appliance

## Back-end Scripts
### **generateKey.py** 
>Called by the web front-end after a user successfully logs in and hits Submit
```
# python26 generateKey.py
A4VASU5475L2IQE2
```
### **VPNenrollment-new.exp**
>Creates an SSH session to VPN appliance to add user key pairing

### **VPNenrollment-update.exp**
>Creates an SSH session to VPN appliance to update existing user key pairing

## Logging & Troubleshooting
### **OTPcheck.py [argv1]**
>Checks system generated OTP, useful for comparing user device generated OTP
```
# python26 OTPcheck.py A4VASU5475L2IQE2
('Current OTP:', u'403545')
```
