# bms-playbook

## How to execute the playbook

```
# Create your inventory file
$ vim inventories/production/hosts

$ cat inventories/production/hosts
all:
  children:
    web:
      hosts:
        13.231.137.71:  # <= your server
  vars:
    aws_access_key_id: YOUR_AWS_ACCESS_KEY
    aws_secret_access_key: YOUR_AWS_SECRET_KEY

# dry run
$ ansible-playbook -i inventories/production/hosts -K --check --diff --skip-tags ecr main_playbook.yml
BECOME password:  # <= ubuntu's password

# Apply
$ ansible-playbook -i inventories/production/hosts -K --diff main_playbook.yml
BECOME password:  # <= ubuntu's password


# Apply only tasks associated with AWS ECR and the containers
$ ansible-playbook -i inventories/production/hosts -K --diff --start-at-task="Start AWS ECR tasks" main_playbook.yml
BECOME password:  # <= ubuntu's password
```


<!--
### EC2 - inventory

1. Get remote files
```
$ wget https://raw.githubusercontent.com/ansible/ansible/devel/contrib/inventory/ec2.ini
$ wget https://raw.githubusercontent.com/ansible/ansible/devel/contrib/inventory/ec2.py
```
 -->

<!--
## Link

- [» Ansibleの標準モジュールでEC2のサーバー構築をしてみる TECHSCORE BLOG](http://www.techscore.com/blog/2015/06/02/ansible%E3%81%AE%E6%A8%99%E6%BA%96%E3%83%A2%E3%82%B8%E3%83%A5%E3%83%BC%E3%83%AB%E3%81%A7ec2%E3%81%AE%E3%82%B5%E3%83%BC%E3%83%90%E3%83%BC%E6%A7%8B%E7%AF%89%E3%82%92%E3%81%97%E3%81%A6%E3%81%BF%E3%82%8B/)
- [AnsibleのDynamic InventoryでAWS EC2管理 - Qiita](https://qiita.com/teru1000/items/d8d292186aee6c631ee0)
- [\[AWS\]AnsibleのDynamic Inventoryを使って実行対象のEC2をタグ等で柔軟に指定する ｜ DevelopersIO](https://dev.classmethod.jp/cloud/aws/ansible-dynamic-inventory-2/)

- https://qiita.com/juhn/items/274e44ee80354a39d872

### ansible modules

- [ansible\.posix\.authorized\_key – Adds or removes an SSH authorized key — Ansible Documentation](https://docs.ansible.com/ansible/latest/collections/ansible/posix/authorized_key_module.html)
- [ansible\.builtin\.user – Manage user accounts — Ansible Documentation](https://docs.ansible.com/ansible/latest/collections/ansible/builtin/user_module.html) -->
