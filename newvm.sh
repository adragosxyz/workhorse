# Update Packages
apt-get update
# Upgrade Packages
apt-get upgrade

sudo su

head /dev/urandom -c 16 | xxd -p > /root/userpass
printf "%s\n%s\n" `cat /root/userpass` `cat /root/userpass` | passwd vagrant

cp /vagrant/keys /home/vagrant/.ssh/authorized_keys

cat /etc/hostname > /vagrant/dockername

#start-stop-daemon --start --background -m --oknodo --pidfile /var/run/vsftpd/vsftpd.pid --exec /usr/sbin/vsftpd