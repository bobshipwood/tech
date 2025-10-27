#!/bin/bash

for image in ./calico-images/*.tar; do
  docker load -i "$image"
done

