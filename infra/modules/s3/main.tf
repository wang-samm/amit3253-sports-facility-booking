# AWS Academy explicitly denies s3:GetBucketObjectLockConfiguration. The AWS
# provider calls that API while refreshing aws_s3_bucket, so this lab-safe
# Terraform resource invokes the AWS CLI instead. It is still created and
# destroyed by Terraform running in GitHub Actions.
resource "terraform_data" "uploads" {
  input = var.bucket_name

  provisioner "local-exec" {
    interpreter = ["/bin/bash", "-c"]
    command     = <<-EOT
      set -euo pipefail
      if ! aws s3api head-bucket --bucket "${var.bucket_name}" 2>/dev/null; then
        aws s3api create-bucket --bucket "${var.bucket_name}" --region us-east-1
      fi
      aws s3api put-bucket-encryption \
        --bucket "${var.bucket_name}" \
        --server-side-encryption-configuration '{"Rules":[{"ApplyServerSideEncryptionByDefault":{"SSEAlgorithm":"AES256"}}]}'
      aws s3api put-public-access-block \
        --bucket "${var.bucket_name}" \
        --public-access-block-configuration BlockPublicAcls=true,IgnorePublicAcls=true,BlockPublicPolicy=true,RestrictPublicBuckets=true
    EOT
  }

  provisioner "local-exec" {
    when        = destroy
    interpreter = ["/bin/bash", "-c"]
    command     = <<-EOT
      aws s3 rm "s3://${self.output}" --recursive || true
      aws s3api delete-bucket --bucket "${self.output}" || true
    EOT
  }
}

