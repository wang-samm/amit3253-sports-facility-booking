variable "name_prefix" {
  description = "Prefix used for monitoring resource names."
  type        = string
}

variable "alert_email" {
  description = "Optional email address for SNS alerts."
  type        = string
  default     = ""
}

variable "asg_name" {
  description = "Auto Scaling group name used by the EC2 CPU alarm."
  type        = string
}

variable "alb_arn_suffix" {
  description = "ALB ARN suffix used as the CloudWatch LoadBalancer dimension."
  type        = string
}

variable "target_group_arn_suffix" {
  description = "Target group ARN suffix used as the CloudWatch TargetGroup dimension."
  type        = string
}

variable "db_identifier" {
  description = "RDS instance identifier used by the database alarms."
  type        = string
}
