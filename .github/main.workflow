workflow "On push" {
  on = "push"
  resolves = ["ansible/ansible-lint-action@master"]
}

workflow "On PR" {
  on = "pull_request"
  resolves = ["ansible/ansible-lint-action@master"]
}
