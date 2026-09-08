terraform {
  # GitHub Actions supplies the account-specific S3 bucket and DynamoDB lock
  # table during terraform init. Never commit local state or credentials.
  backend "s3" {}
}
