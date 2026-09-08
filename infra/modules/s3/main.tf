# assignment-s3-uploads: holds facility photos and release artifacts.
# only unauthenticated GetObject under uploads/* is allowed via bucket policy so
# images render in the browser, without allowing public listing or writes.
resource "aws_s3_bucket" "uploads" {
  bucket = var.bucket_name

  # Sandbox environment: by the time you `terraform destroy`, this bucket will
  # contain uploaded facility images, deploy.yml release artifacts, and
  # db-init.yml's schema.sql/seed-db.sh. AWS refuses to delete a non-empty
  # bucket, so without force_destroy the destroy would fail on this resource.
  force_destroy = true

  tags = {
    Name = "${var.name_prefix}-s3-uploads"
  }
}

resource "aws_s3_bucket_public_access_block" "uploads" {
  bucket = aws_s3_bucket.uploads.id

  block_public_acls       = true
  ignore_public_acls      = true
  block_public_policy     = true
  restrict_public_buckets = true
}

resource "aws_s3_bucket_cors_configuration" "uploads" {
  bucket = aws_s3_bucket.uploads.id

  cors_rule {
    allowed_headers = ["*"]
    allowed_methods = ["GET"]
    allowed_origins = ["*"]
    max_age_seconds = 3000
  }
}
