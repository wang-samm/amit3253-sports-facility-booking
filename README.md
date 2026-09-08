# AMIT3253 Sports Facility Booking System

This project adapts the lecturer's PHP/MySQL sample into a TAR UMT sports-facility booking platform and deploys it to AWS entirely through Terraform executed by GitHub Actions.

## What was reused and what was replaced

Safe reusable components:

- Login, registration, account management, password hashing and admin authorization
- Prepared MySQL statements and escaped HTML output
- Contact messages, testimonials, image validation and light/dark theme
- Terraform modules for VPC, ALB, Auto Scaling, RDS, S3, Secrets Manager and security groups
- GitHub Actions workflow structure and SSM-based application deployment

Event-only components were replaced:

- `events`, `orders`, `tickets` and `seats` database tables
- Ticket payment, assigned-seat picker, QR codes and event check-in
- Event CRUD pages, event seed data, workflow paths and EC2 tags

The replacement model is `facilities -> courts -> time_slots -> bookings`, with `closures` for maintenance or unavailable periods.

## Functional features

- Browse and search sports facilities
- Live date/court/time-slot schedule
- Create, view, edit and cancel bookings
- Transactional locking to prevent concurrent double booking
- Registration, login and profile/password management
- Admin management of facilities, courts, closures, bookings, users, testimonials and messages
- Private S3 facility-image storage using short-lived signed display URLs
- RDS-aware ALB health check

Demo accounts (change before final presentation):

- Admin: `admin@example.com` / `password`
- Student: `student@example.com` / `password`

## AWS architecture

- One custom VPC in `us-east-1`
- Two public subnets across two Availability Zones for the public ALB
- Two private subnets across two Availability Zones for EC2 Auto Scaling and private RDS
- NAT Gateway for private-instance package installation, SSM and AWS API access
- Application Load Balancer as the only public application entry point
- Auto Scaling Group: minimum 2, desired 2, maximum 4
- CPU target-tracking policy at 60%
- Single-AZ MySQL RDS (the assignment's stated POC assumption)
- Least-privilege ALB, EC2 and RDS security-group paths
- Public/private Network ACLs
- Secrets Manager for generated database credentials
- Private S3 bucket for application releases and facility photos

## GitHub Actions

| Workflow | Purpose |
|---|---|
| `CI - Full Pipeline` | Main manual entry point: plan, deploy or destroy |
| `CI - Build Infrastructure` | Bootstraps remote state, then runs Terraform |
| `CD - Deploy Application` | Applies infrastructure, packages PHP and deploys it through SSM |
| `DB - Seed Database` | Imports `schema.sql` into private RDS once |

## Before the first run

1. Create a new private GitHub repository.
2. Upload this complete directory without changing its folder structure.
3. Start the AWS Academy Learner Lab.
4. Open **AWS Details -> AWS CLI** and copy the three temporary values.
5. In GitHub, open **Settings -> Secrets and variables -> Actions**.
6. Create these repository secrets:
   - `AWS_ACCESS_KEY_ID`
   - `AWS_SECRET_ACCESS_KEY`
   - `AWS_SESSION_TOKEN`
7. Refresh all three secrets whenever the Academy session credentials expire.

Never place credentials in `.tf`, `.tfvars`, PHP files, commits, screenshots or reports.

## First deployment

1. In GitHub, open **Actions -> CI - Full Pipeline -> Run workflow**.
2. Choose `plan`. Check that Terraform proposes the expected resources.
3. Run the same workflow again with `deploy`.
4. Wait for infrastructure creation and application deployment to finish.
5. Run **Actions -> DB - Seed Database (one-time)** once.
6. In the successful infrastructure workflow output, obtain `alb_dns_name`, or open AWS EC2 -> Load Balancers and copy the ALB DNS name.
7. Browse to `http://<alb-dns-name>`.

The first workflow run automatically creates an encrypted, versioned S3 Terraform-state bucket and DynamoDB locking table. These backend resources are intentionally retained after `destroy` so later deployments keep state history.

## Normal update cycle

1. Change and test code.
2. Push to GitHub.
3. Refresh the three Academy credentials in GitHub Secrets.
4. Run `CI - Full Pipeline` with `deploy`.

## Stop charges

After screenshots and demonstrations, run `CI - Full Pipeline` with `destroy`. This removes the VPC workload, NAT Gateway, ALB, EC2, RDS, Secrets Manager secret and application S3 bucket. The remote-state bucket and lock table remain and cost almost nothing at assignment scale.

## Evidence checklist for the report

- Architecture diagram and user/data-flow diagram
- GitHub Actions successful Terraform plan/apply/deploy runs
- Terraform code screenshots showing modules
- Public ALB URL and healthy targets across two Availability Zones
- Private EC2 instances and private RDS with no public address
- Security Group and Network ACL rules
- Successful create/read/update/cancel booking and matching RDS rows
- CPU scaling policy and ASG activity during a Locust/JMeter/Apache Bench test
- CloudWatch metrics: CPU, request count, response time, healthy hosts
- AWS Pricing Calculator export for 12 months in `us-east-1`
- Successful `terraform destroy` after testing

