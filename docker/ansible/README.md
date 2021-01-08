## Onboarding setup

## Requirements

- ansible

## Preparation
```
% python3 -m venv venv ;\
  source  venv/bin/activate ;\
  pip3 install ansible
```

## Run playbook

```
# dryrun
% ansible-playbook --check --diff initializer.yaml

# apply playbook
% ansible-playbook --diff initializer.yaml

# apply `gem` tag objects only.
% ansible-playbook --check --diff --tags gem initializer.yaml
```
