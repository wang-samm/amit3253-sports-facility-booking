output "bucket_id" {
  value = terraform_data.uploads.output
}

output "bucket_arn" {
  value = "arn:aws:s3:::${terraform_data.uploads.output}"
}

output "bucket_regional_domain_name" {
  value = "${terraform_data.uploads.output}.s3.us-east-1.amazonaws.com"
}
