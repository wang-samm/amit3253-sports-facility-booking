# Terraform Infrastructure Notes

Run Terraform through `.github/workflows/ci.yml`; GitHub Actions is the deployment runner.

Modules:

- `vpc`: VPC, two public and two private subnets, Internet Gateway, NAT Gateway, routes, S3 endpoint and Network ACLs
- `security-groups`: ALB public HTTP, EC2 HTTP only from ALB, RDS MySQL only from EC2
- `alb`: public Application Load Balancer, listener and health-checked target group
- `asg`: Amazon Linux 2023 launch template, two-AZ Auto Scaling Group and CPU target tracking
- `rds`: private single-AZ MySQL database
- `secrets`: generated database credentials in Secrets Manager
- `s3`: private shared image and application-artifact bucket

The state backend is configured dynamically by GitHub Actions using the current AWS account ID. Do not insert a fixed account ID into `backend.tf`.

This design uses a NAT Gateway so private EC2 instances can install packages, register with SSM and retrieve AWS resources. It is suitable for the rubric but is the largest avoidable POC cost. Deploy only for tests and demonstrations, then destroy the workload.
